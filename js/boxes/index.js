/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

const $ = window.$;

$(() => {
  // $(document).ready(function() {
    const boxesGrid = new window.prestashop.component.Grid('id_box');

    boxesGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
    boxesGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
    boxesGrid.addExtension(new window.prestashop.component.GridExtensions.PositionExtension(boxesGrid));
    boxesGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
    boxesGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
    boxesGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
    boxesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
    boxesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
    boxesGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
    boxesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
    boxesGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  // });
});
