<?php

namespace Module\IndexBoxes\Controller\Admin;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Exception\TableExistsException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Module\IndexBoxes\Entity\IndexBox;
use Module\IndexBoxes\Entity\IndexBoxLang;
use Module\IndexBoxes\Grid\Definition\Factory\IndexBoxGridDefinitionFactory;
use Module\IndexBoxes\Grid\Filters\IndexBoxFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;

class IndexBoxesController extends FrameworkBundleAdminController
{
    /**
     * List boxes
     *
     * @param IndexBoxFilters $filters
     *
     * @return Response
     */
    public function indexAction(IndexBoxFilters $filters)
    {
        $boxGridFactory = $this->get('prestashop.module.indexboxes.grid.factory.boxes');
        $boxGrid = $boxGridFactory->getGrid($filters);

        return $this->render(
            '@Modules/indexboxes/views/templates/admin/index.html.twig',
            [
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Boxes', 'Modules.Indexboxes.Admin'),
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'boxGrid' => $this->presentGrid($boxGrid),
            ]
        );
    }

    /**
     * Provides filters functionality.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.module.indexboxes.grid.definition.factory.boxes'),
            $request,
            IndexBoxGridDefinitionFactory::GRID_ID,
            'ps_indexboxes_box_index'
        );
    }

    /**
     * List boxes
     *
     * @param Request $request
     *
     * @return Response
     */
    public function generateAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $generator = $this->get('prestashop.module.indexboxes.boxes.generator');
            $generator->generateBoxes();
            $this->addFlash('success', $this->trans('Boxes were successfully generated.', 'Modules.Indexboxes.Admin'));

            return $this->redirectToRoute('ps_indexboxes_box_index');
        }

        return $this->render(
            '@Modules/indexboxes/views/templates/admin/generate.html.twig',
            [
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Boxes', 'Modules.Indexboxes.Admin'),
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            ]
        );
    }

    /**
     * Create box
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $boxFormBuilder = $this->get('prestashop.module.indexboxes.form.identifiable_object.builder.box_form_builder');
        $boxForm = $boxFormBuilder->getForm();
        $boxForm->handleRequest($request);

        $boxFormHandler = $this->get('prestashop.module.indexboxes.form.identifiable_object.handler.box_form_handler');
        $result = $boxFormHandler->handle($boxForm);

        if (null !== $result->getIdentifiableObjectId()) {
            $this->addFlash(
                'success',
                $this->trans('Successful creation.', 'Admin.Notifications.Success')
            );

            return $this->redirectToRoute('ps_indexboxes_box_index');
        }

        return $this->render('@Modules/indexboxes/views/templates/admin/create.html.twig', [
            'boxForm' => $boxForm->createView(),
        ]);
    }

    /**
     * Edit box
     *
     * @param Request $request
     * @param int $boxId
     *
     * @return Response
     */
    public function editAction(Request $request, $boxId)
    {
        $boxFormBuilder = $this->get('prestashop.module.indexboxes.form.identifiable_object.builder.box_form_builder');
        $boxForm = $boxFormBuilder->getFormFor((int) $boxId);
        $boxForm->handleRequest($request);

        $boxFormHandler = $this->get('prestashop.module.indexboxes.form.identifiable_object.handler.box_form_handler');
        $result = $boxFormHandler->handleFor((int) $boxId, $boxForm);

        if ($result->isSubmitted() && $result->isValid()) {
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

            return $this->redirectToRoute('ps_indexboxes_box_index');
        }

        return $this->render('@Modules/indexboxes/views/templates/admin/edit.html.twig', [
            'boxForm' => $boxForm->createView(),
        ]);
    }

    /**
     * Delete box
     *
     * @param int $boxId
     *
     * @return Response
     */
    public function deleteAction($boxId)
    {
        $repository = $this->get('prestashop.module.indexboxes.repository.box_repository');
        try {
            $box = $repository->findOneById($boxId);
        } catch (EntityNotFoundException $e) {
            $box = null;
        }

        if (null !== $box) {
            $repository->movePositions($box->getId());
            $box->deleteImage();
            /** @var EntityManagerInterface $em */
            $em = $this->get('doctrine.orm.entity_manager');
            $em->remove($box);
            $em->flush();

            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } else {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot find box %box%',
                    'Modules.Indexboxes.Admin',
                    ['%box%' => $boxId]
                )
            );
        }

        return $this->redirectToRoute('ps_indexboxes_box_index');
    }

    /**
     * Toggle status box
     *
     * @param Request $request
     *
     * @return Response
     */
    public function changePosition(Request $request)
    {
        $positionsData = [
            'positions' => $request->request->get('positions'),
        ];

        $positionDefinition = new PositionDefinition('index_box', 'id_box', 'position');
        // $positionDefinition = $this->get('prestashop.module.indexboxes.grid.index_box.position_definition');
        $positionUpdateFactory = $this->get('prestashop.core.grid.position.position_update_factory');

        try {
            $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);
            $updater = $this->get('prestashop.core.grid.position.doctrine_grid_position_updater');
            $updater->update($positionUpdate);
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (TranslatableCoreException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);
        } catch (Exception $e) {
            $this->flashErrors([$e->getMessage()]);
        }

        return $this->redirectToRoute('ps_indexboxes_box_index');
    }

    /**
     * Toggle status box
     *
     * @param Request $request
     *
     * @return Response
     */
    public function toggleStatus($boxId)
    {
        $repository = $this->get('prestashop.module.indexboxes.repository.box_repository');
        try {
            $box = $repository->findOneById($boxId);
        } catch (EntityNotFoundException $e) {
            $box = null;
        }
        if ($box) {
            /** @var EntityManagerInterface $em */
            $em = $this->get('doctrine.orm.entity_manager');
            $box->setActive(!$box->isActive());
            $em->flush();

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('ps_indexboxes_box_index');
    }

    /**
     * Enable bulk boxes
     *
     * @param Request $request
     *
     * @return Response
     */
    public function enableBulkAction(Request $request)
    {
        $boxIds = $request->request->get('id_box_bulk');
        $repository = $this->get('prestashop.module.indexboxes.repository.box_repository');
        try {
            $boxes = $repository->findById($boxIds);
        } catch (EntityNotFoundException $e) {
            $boxes = null;
        }
        if (!empty($boxes)) {
            /** @var EntityManagerInterface $em */
            $em = $this->get('doctrine.orm.entity_manager');
            foreach ($boxes as $box) {
                $box->setActive(true);
            }
            $em->flush();

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully enabled.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('ps_indexboxes_box_index');
    }

    /**
     * Disable bulk boxes
     *
     * @param Request $request
     *
     * @return Response
     */
    public function disableBulkAction(Request $request)
    {
        $boxIds = $request->request->get('id_box_bulk');
        $repository = $this->get('prestashop.module.indexboxes.repository.box_repository');
        try {
            $boxes = $repository->findById($boxIds);
        } catch (EntityNotFoundException $e) {
            $boxes = null;
        }
        if (!empty($boxes)) {
            /** @var EntityManagerInterface $em */
            $em = $this->get('doctrine.orm.entity_manager');
            foreach ($boxes as $box) {
                $box->setActive(false);
            }
            $em->flush();

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully disabled.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('ps_indexboxes_box_index');
    }

    /**
     * Delete bulk boxes
     *
     * @param Request $request
     *
     * @return Response
     */
    public function deleteBulkAction(Request $request)
    {
        $boxIds = $request->request->get('id_box_bulk');
        $repository = $this->get('prestashop.module.indexboxes.repository.box_repository');
        try {
            $boxes = $repository->findById($boxIds);
        } catch (EntityNotFoundException $e) {
            $boxes = null;
        }
        if (!empty($boxes)) {
            /** @var EntityManagerInterface $em */
            $em = $this->get('doctrine.orm.entity_manager');
            foreach ($boxes as $box) {
                $repository->movePositions($box->getId());
                $box->deleteImage();
                $em->remove($box);
                $em->flush();
            }

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('ps_indexboxes_box_index');
    }

    /**
     * @return array[]
     */
    private function getToolbarButtons()
    {
        return [
            'add' => [
                'desc' => $this->trans('Add new box', 'Modules.Indexboxes.Admin'),
                'icon' => 'add_circle_outline',
                'href' => $this->generateUrl('ps_indexboxes_box_create'),
            ],
            'generate' => [
                'desc' => $this->trans('Generate boxes', 'Modules.Indexboxes.Admin'),
                'icon' => 'add_circle_outline',
                'href' => $this->generateUrl('ps_indexboxes_box_generate'),
            ],
        ];
    }
}
