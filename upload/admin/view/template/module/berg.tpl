<?php echo $header; ?>
<div id="content">
  <div class="box">
    <div class="heading">
      <h1><?php echo $heading_title; ?></h1>
      <div class="buttons">
        <a href="#" onclick="$('#form').submit(); return false;" class="button">Сохранить</a>
      </div>
    </div>
    <div class="content">
      <div id="tabs" class="htabs">
        <a href="#tab-api">API</a>
        <a href="#tab-sale">Товары</a>
      </div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-api">
          <table class="form">
            <tr>
              <td>Ключ:</td>
              <td><input type="text" name="key" style="width:500px;" value="<?php echo $conf['key']; ?>" /></td>
            </tr>
            <tr>
              <td><label for="conf_analogs">Показывать аналоги:</label></td>
              <td><input id="conf_analogs" type="checkbox" name="analogs" value="1" <?php echo $conf['analogs'] ? 'checked' : '';?>></td>
            </tr>
          </table>
        </div>
        <div id="tab-sale">
          <table class="form">
            <tr>
              <td>Наценка:</td>
              <td><input type="text" name="overprice" value="<?php echo $conf['overprice']; ?>" /></td>
            </tr>
            <tr>
              <td>Погрешность доставки:</td>
              <td><input type="text" name="delivery" value="<?php echo $conf['delivery']; ?>" /> дней</td>
            </tr>
            <tr>
              <td>Категория для хранения товаров:</td>
              <td>
                <select name="category">
                  <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo $category['category_id']; ?>" <?php echo ($conf['category'] == $category['category_id']) ? 'selected' : ''; ?>><?php echo $category['name']; ?></option>
                  <?php } ?>
                </select>
              </td>
            </tr>
          </table>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script>

<?php echo $footer; ?>
