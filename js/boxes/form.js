/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

// Since PrestaShop 1.7.8 you can import components below directly from the window object.
// This is the recommended way to use them as you won't have to do any extra configuration or compilation.
// We keep the old way of importing them for backward compatibility.
// @see: https://devdocs.prestashop-project.org/1.7/development/components/global-components/

const $ = window.$;

$(() => {
  new window.prestashop.component.TranslatableInput();
  var tree = new window.prestashop.component.ChoiceTree('#index_box_category_id');

  let image = $('input[name="index_box[image]"]');
  if($(image).val()) {
    $(image).parent().append('<img class="img-fluid" src="' + $(image).val() + '"/>')
  }

  var type_selector = $('select[name="index_box[type]"]')[0];
  var rows = [];
  rows['product'] = $('label[for="index_box_product_id"]').parents('.form-group')[0];
  rows['category'] = $('label[for="index_box_category_id"]').parents('.form-group')[0];
  rows['cms'] = $('label[for="index_box_cms_id"]').parents('.form-group')[0];
  let current = $(type_selector).val();
  let current_id = $('input[name="index_box[item_id]"]').val();
  let item = $('*[name="index_box[' + current + '_id]"][value=' + current_id +']');
  if(item.length > 0) {
    $(item).attr('checked', 'checked')
    $(item).parents('.collapsed').removeClass('collapsed').addClass('extended');
  } else {
    $('*[name="index_box[' + current + '_id]"] option[value=' + current_id +']').attr('selected', 'selected');
  }
  function updateRows() {
    let current = $(type_selector).val();
    for(const index in rows) {
      if(index != current) {
        $(rows[index]).hide();
      } else {
        $(rows[index]).show();
      }
    }
  }
  updateRows();
  type_selector.addEventListener('change', updateRows);
});
