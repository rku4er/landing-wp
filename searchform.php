<?php $form_id = uniqid('search-'); ?>

<form
  role="search"
  method="get"
  class="search-form form-inline"
  action="<?php echo esc_url(home_url('/')); ?>">

  <label
    for="<?php echo $form_id; ?>"
    type="button">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon">
      <use xlink:href="#magnifier"></use>
    </svg>
  </label>

  <div class="form-group">

    <div class="input-group">

      <input
        class="search-field form-control"
        type="search"
        value="<?php echo get_search_query(); ?>"
        id="<?php echo $form_id; ?>"
        name="s"
        placeholder="<?php _e('Search', 'sage'); ?>"
        required />

      <span class="input-group-btn">
        <button
          type="submit"
          class="search-submit btn btn-primary">
          <?php _e('Search', 'sage'); ?>
        </button>
      </span>

    </div>
  </div>

</form>
