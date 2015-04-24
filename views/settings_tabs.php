    <?php  
        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'settings'; 
    ?>  

	<h2 class="nav-tab-wrapper">  

        <a href="<?php echo $wpl_settings_url; ?>&tab=settings"   class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php echo __('General Settings','wpla') ?></a>  
        <a href="<?php echo $wpl_settings_url; ?>&tab=accounts"   class="nav-tab <?php echo $active_tab == 'accounts' ? 'nav-tab-active' : ''; ?>"><?php echo __('Accounts','wpla') ?></a>  
        <a href="<?php echo $wpl_settings_url; ?>&tab=categories" class="nav-tab <?php echo $active_tab == 'categories' ? 'nav-tab-active' : ''; ?>"><?php echo __('Categories','wpla') ?></a>  
        <a href="<?php echo $wpl_settings_url; ?>&tab=advanced"   class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>"><?php echo __('Advanced','wpla') ?></a>

        <?php if ( ! defined('WPLISTER_RESELLER_VERSION') || ( $active_tab == 'developer' ) ) : ?>
        <a href="<?php echo $wpl_settings_url; ?>&tab=developer"  class="nav-tab <?php echo $active_tab == 'developer' ? 'nav-tab-active' : ''; ?>"><?php echo __('Developer','wpla') ?></a>  
        <?php endif; ?>


    </h2>  
