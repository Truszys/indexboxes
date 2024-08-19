<?php

namespace Module\IndexBoxes\Grid\Definition\Factory;

use Module\IndexBoxes\Entity\IndexBox;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DraggableColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;

class IndexBoxGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'id_box';

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Boxes', [], 'Modules.Indexboxes.Admin');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_box',
                ])
            )
            ->add((new DraggableColumn('position_drag')))
            ->add((new DataColumn('id_box'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_box',
                ])
            )
            ->add((new ImageColumn('image'))
                ->setName($this->trans('Image', [], 'Admin.Global'))
                ->setOptions([
                    'src_field' => 'image',
                ])
            )
            ->add((new DataColumn('bo_title'))
                ->setName($this->trans('Title', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'bo_title',
                ])
            )
            ->add((new DataColumn('type'))
                ->setName($this->trans('Type', [], 'Modules.Indexboxes.Admin'))
                ->setOptions([
                    'field' => 'type',
                ])
            )
            ->add((new PositionColumn('position'))
                ->setName($this->trans('Position', [], 'Admin.Global'))
                ->setOptions([
                    'id_field' => 'id_box',
                    'position_field' => 'position',
                    'update_route' => 'ps_indexboxes_box_change_position',
                    'update_method' => 'POST',
                    // 'record_route_params' => [
                    //     'id_box' => 'boxId',
                    // ],
                ])
            )
            ->add((new ToggleColumn('active'))
                ->setName($this->trans('Displayed', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active',
                    'primary_field' => 'id_box',
                    'route' => 'ps_indexboxes_box_toggle_status',
                    'route_param_name' => 'boxId',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'ps_indexboxes_box_edit',
                                'route_param_name' => 'boxId',
                                'route_param_field' => 'id_box',
                                'clickable_row' => true,
                            ])
                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'DELETE',
                                'route' => 'ps_indexboxes_box_delete',
                                'route_param_name' => 'boxId',
                                'route_param_field' => 'id_box',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                            ])
                        ),
                ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_box', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('ID', [], 'Admin.Global'),
                    ],
                ])
                ->setAssociatedColumn('id_box')
            )
            ->add((new Filter('bo_title', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('title', [], 'Modules.Indexboxes.Admin'),
                    ],
                ])
                ->setAssociatedColumn('bo_title')
            )
            ->add((new Filter('type', YesAndNoChoiceType::class))
                ->setTypeOptions([
                    'choices' => IndexBox::$TYPES,
                    'attr' => [
                        'placeholder' => $this->trans('Type', [], 'Modules.Indexboxes.Admin'),
                    ],
                ])
                ->setAssociatedColumn('type')
            )
            ->add(
                (new Filter('active', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('active')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search_by_filter_id',
                    'reset_route_params' => [
                        'filterId' => self::GRID_ID,
                    ],
                    'redirect_route' => 'ps_indexboxes_box_index',
                ])
                ->setAssociatedColumn('actions')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add((new SimpleGridAction('common_refresh_list'))
                ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                ->setIcon('refresh')
            )
            ->add((new SimpleGridAction('common_show_query'))
                ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                ->setIcon('code')
            )
            ->add((new SimpleGridAction('common_export_sql_manager'))
                ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                ->setIcon('storage')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('enable_bulk'))
                ->setName($this->trans('Enabled selected', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'ps_indexboxes_box_bulk_enable',
                    'confirm_message' => $this->trans('Enable selected items?', [], 'Admin.Notifications.Warning'),
                ])
            )
            ->add((new SubmitBulkAction('disable_bulk'))
                ->setName($this->trans('Disabled selected', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'ps_indexboxes_box_bulk_disable',
                    'confirm_message' => $this->trans('Disable selected items?', [], 'Admin.Notifications.Warning'),
                ])
            )
            ->add((new SubmitBulkAction('delete_bulk'))
                ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'ps_indexboxes_box_bulk_delete',
                    'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                ])
            )
        ;
    }
}
