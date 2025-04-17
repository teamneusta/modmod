<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Controller;

use Neusta\Modmod\Domain\Repository\CommentRepository;
use Neusta\Modmod\Provider\FormValueProvider;
use Neusta\Modmod\Provider\PagetreeProvider;
use Neusta\Modmod\Utility\BackendUserUtility;
use Neusta\Modmod\Utility\PagetreeUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\Controller;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

#[Controller]
class BackendModerateCommentsController extends ActionController
{
    public const CMD_PUBLISH = 'publish';
    public const CMD_UNPUBLISH = 'unpublish';

    private BackendUriBuilder $beUriBuilder;

    private CommentRepository $commentRepository;

    private FormValueProvider $formValueProvider;

    private PagetreeProvider $pageProvider;

    public function __construct(
        PagetreeProvider $pageProvider,
        FormValueProvider $formValueProvider,
        CommentRepository $commentRepository,
        BackendUriBuilder $uriBuilder,
        private ModuleTemplateFactory $moduleTemplateFactory,
    ) {
        $this->pageProvider = $pageProvider;
        $this->commentRepository = $commentRepository;
        $this->beUriBuilder = $uriBuilder;
        $this->formValueProvider = $formValueProvider;
    }

    public function indexAction(): ResponseInterface
    {
        $pageId = $this->getCurrentPageId();
        $depth = (int)$this->formValueProvider->getStoredValue($this->request->getPluginName(), 'depth');
        $pageTree = $this->pageProvider->getPageTree($pageId, $depth);
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $uidList = PagetreeUtility::getPageIdArray($pageTree);

        $moduleTemplate->assignMultiple([
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

        return $moduleTemplate->renderResponse('Index');
    }

    public function toggleVisibilityAction(): ResponseInterface
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

        return new ForwardResponse('index');
    }

    public function deleteAction(): ResponseInterface
    {
        $commentUid = (int)($this->request->getArgument('comment') ?? 0);
        if ($commentUid > 0) {
            $this->commentRepository->deleteComment($commentUid);
        }

        return new ForwardResponse('index');
    }

    protected function getCurrentPageId(): int
    {
        $pageId = (int) $this->getRequest()->getQueryParams()['id'];

        return $pageId === 0 ? 1 : $pageId;
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
    }
}
