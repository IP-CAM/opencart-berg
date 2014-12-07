<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="berg">
  <?php echo $content_top; ?>

  <form class="berg_search">
    <input type="hidden" name="route" value="module/berg/search" />
    <input class="berg_search_query" type="text" name="query" value="<?php echo $query; ?>" placeholder="Поиск по артикулу или названию запчасти" /><!--
    --><button class="berg_search_button" type="submit">Найти</button>
  </form>

  <?php if (count($products)) { ?>
    <div class="berg_results">
      <table class="berg_products">
        <thead>
          <tr>
            <th style="width: 45%;" class="berg_products_cell -name">Наименование</th>
            <th style="width: 10%;" class="berg_products_cell -brand">Производитель</th>
            <th style="width: 5%;" class="berg_products_cell -quantity">Количество</th>
            <th style="width: 10%;" class="berg_products_cell -delivery">Доставка</th>
            <th style="width: 10%;" class="berg_products_cell -price">Цена</th>
            <th style="width: 20%;" class="berg_products_cell -buy"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product) { ?>
            <tr class="berg_product">
              <td><?php echo $product['name']; ?></td>
              <td><?php echo $product['brand']; ?></td>
              <td><?php echo $product['quantity']; ?></td>
              <td>от <?php echo $product['delivery']; ?> дней</td>
              <td><?php echo $product['price']; ?> руб.</td>
              <td>
                <form class="berg_product_buy" method="post">
                  <input type="hidden" name="product_uid" value="<?php echo $product['uid']; ?>" />
                  <input class="berg_product_buy_quantuty" type="number" name="quantity" value="1" max="<?php echo $product['quantity']; ?>" />
                  <button class="berg_product_buy_button">Купить</button>
                </form>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  <?php } ?>

  <?php echo $content_bottom; ?>
</div>

<script>$(function() {

  $('.berg_product_buy').on('submit', function(e) {
    var data = $(this).serializeArray();
    var $button = $(this).find('button');

    $button.text('Подождите').attr('disabled', true);

    $.post('/index.php?route=module/berg/addtocart', data).done(function(res) {
      if (res) {
        alert('Товар «' + res.product.name + '» добавлен в корзину.');
        $button.text('Купить').attr('disabled', false);
        $('#cart-total').html(res.total);
      }
    });
    e.preventDefault();
  });

});</script>

<?php echo $footer; ?>
