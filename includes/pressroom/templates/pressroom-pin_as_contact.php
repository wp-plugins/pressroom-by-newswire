<!-- Add this code to person page: -->
<?php     
    $_data = get_post_meta(get_the_ID(), 'newswire_data', true ); 
    extract($_data);
?>
<div class="block-header">
    <h2 class="title"><?php echo apply_filters('pin_as_contact_title', get_the_title() );?></h2>
</div>
<div class="block-content" id="content-uid-<?php echo get_the_ID() ?>">
 <div itemscope itemprop="author" itemtype="http://schema.org/Person">
    <?php newswire_ifprint('<meta itemprop="description" content="%s">', ''); ?>
    <?php newswire_ifprint('<meta itemprop="birthDate" content="%s">', ''); ?>
    
    <?php 
        newswire_ifprint('<span itemprop="name"><strong>%s</strong></span>', $first_name .' '.$last_name );
        newswire_ifprint( ' - <span itemprop="jobtitle" style="display:;">%s</span>',  $contact_position ); 
    ?>
    <?php 
        $company_tickers = ( $company_tickers!="") ? "<sup>($company_tickers)</sup>": "";
        newswire_ifprint('<span itemprop="member" itemscope itemtype="http://schema.org/Organization" style="display:block; "><strong>%s</strong> %s</span> ', array(ucfirst($company_name), strtoupper($company_tickers))); ?>
    <?php //newswire_ifprint(' - %s ', $company_tickers); ?>
    <?php // newswire_ifprint('<span itemprop="jobtitle" style="display:block;">%s</span>', $contact_position); ?>
    
    <?php if ( $company_address !=''  ||  $company_city != '' || $company_state !='' || $company_country!= 'none') : ?>
    <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
        <?php 
            newswire_ifprint('<span itemprop="streetAddress" style="display:block;">%s</span>',$company_address); ?>
        <?php 

            newswire_ifprint('<span itemprop="addressLocality" style="display:;">%s</span>', $company_city); 
        ?>
        <?php 
            if ( $company_city != '' && $company_state ) echo ', ';
            newswire_ifprint('<span itemprop="addressRegion"style="display:;">%s</span>', $company_state); 
        ?>
        <?php 
                if ( $company_country != 'none' && $company_state || ($company_city && $company_country!='none') ) echo ', ';
                if ( $company_country != 'none')
                    newswire_ifprint('<span itemprop="addressCountry"style="display:;">%s</span>',$company_country); 

        ?>

        <?php newswire_ifprint('<span style="display:block;">P.O. Box: <span itemprop="postOfficeBoxNumber">%s</span></span>', !empty($company_po_box) ? $company_po_box: ''); ?>
        <div>            
            
        </div>
            <?php newswire_ifprint('<span itemprop="postalCode"style="display:block;">%s</span>', $company_zip) ?>
            
    </div>
    <?php endif; ?>
    <?php newswire_ifprint('<span itemprop="email" style="display:block;"><a href="mailto:%s">%s</a></span>', array($company_email,$company_email));?>
    <?php newswire_ifprint('<span itemprop="telephone" style="display:block;">%s</span>', $company_telephone); ?>
    <?php newswire_ifprint('<a itemprop="url" href="%s">%s</a>', array($company_website,$company_website)); ?>
 </div>
</div><!-- end content //-->
