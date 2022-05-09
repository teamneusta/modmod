<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Controller;

use Neusta\Modmod\Domain\Repository\CommentRepository;
use Neusta\Modmod\Provider\FormValueProvider;
use Neusta\Modmod\Provider\PagetreeProvider;
use Neusta\Modmod\Utility\BackendUserUtility;
use Neusta\Modmod\Utility\PagetreeUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class BackendModerateCommentsController extends ActionController
{
    const CMD_PUBLISH = 'publish';
    const CMD_UNPUBLISH = 'unpublish';

    private BackendUriBuilder $beUriBuilder;

    private CommentRepository $commentRepository;

    private FormValueProvider $formValueProvider;

    private PagetreeProvider $pageProvider;

    public function __construct(
        PagetreeProvider $pageProvider,
        FormValueProvider $formValueProvider,
        CommentRepository $commentRepository,
        BackendUriBuilder $uriBuilder
    ) {
        $this->pageProvider = $pageProvider;
        $this->commentRepository = $commentRepository;
        $this->beUriBuilder = $uriBuilder;
        $this->formValueProvider = $formValueProvider;
    }

    public function indexAction(): void
    {
        $pageId = $this->getCurrentPageId();
        $depth = (int)$this->formValueProvider->getStoredValue($this->request->getPluginName(), 'depth');
        $pageTree = $this->pageProvider->getPageTree($pageId ?? 0, $depth);

        $uidList = PagetreeUtility::getPageIdArray($pageTree);

        $this->view->assignMultiple([
            'moduleName'      => $this->request->getPluginName(),
            'moduleUri'       => (string)$this->beUriBuilder->buildUriFromRoute(
                $this->request->getPluginName(),
                ['id' => $pageId],
            ),
            'commands' => [
                'enable' => self::CMD_PUBLISH,
                'disable' => self::CMD_UNPUBLISH,
            ],
            'tableName'       => CommentRepository::TABLE,
            'constraints'     => [
                'depth' => $depth,
            ],
            'addPageIdPrefix' => BackendUserUtility::getShowPageIdWithTitleOption(),
            'tree'            => $pageTree,
            'comments'        => $this->commentRepository->findByPageIds($uidList),
        ]);
    }

    public function toggleVisibilityAction(): void
    {
        $commentUid = (int)($this->request->getArgument('comment') ?? 0);
        $action = $this->request->getArgument('command') ?? '';
        switch ($action) {
            case self::CMD_PUBLISH:
                $this->commentRepository->publishComment($commentUid);
                break;
            case self::CMD_UNPUBLISH:
                $this->commentRepository->unpublishComment($commentUid);
                break;
            default:
                break;
        }
        $this->forward('index');
    }

    public function deleteAction(): void
    {
        $commentUid = (int)($this->request->getArgument('comment') ?? 0);
        if ($commentUid > 0) {
            $this->commentRepository->deleteComment($commentUid);
        }
        $this->forward('index');
    }

    protected function getCurrentPageId(): int
    {
        return (int)($this->getRequest()->getQueryParams()['id'] ?? 0);
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
    }
}
