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
              <td>Ключ 1:</td>
              <td><input type="text" name="key1" style="width:500px;" value="<?php echo $conf['key1']; ?>" /></td>
            </tr>
            <tr>
              <td>Ключ 2:</td>
              <td><input type="text" name="key2" style="width:500px;" value="<?php echo $conf['key2']; ?>" /></td>
            </tr>
            <tr>
              <td>Регион:</td>
              <td><input type="text" name="region" value="<?php echo $conf['region']; ?>" /></td>
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
              <td><input type="text" name="delivery_from" value="<?php echo $conf['delivery_from']; ?>" style="width: 50px;" /> - <input type="text" name="delivery_to" value="<?php echo $conf['delivery_to']; ?>" style="width: 50px;" /> дней</td>
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
