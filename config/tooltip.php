<?php
# copied from newswire.net website
# tooltips
$newswire_config['tooltip']                                = array();

$newswire_config['tooltip']['pressroom_contact_pin']    = '';

$newswire_config['tooltip']['article_title']               = __('Headlines are the most important part of a news article or press release.  Much of the time that is spent creating an article should be devoted to the title.  Headlines are limited to 70 characters, but good headlines are seldom that long.   Capitalize the first letter of all major words (every word more than 2 or 3 letters - i.e. do no capitalize "a" "to" "for" or other connecting words - do not use all upper case letters.  Do not use punctuation marks in headlines.  Keyword phrase of principle importance should be contained within the text of the title.', 'newswire');
//@todo automate this help file
$newswire_config['tooltip']['author_info_tab'] = '';
//$newswire_config['tooltip']['author_info_tab']             = __('This is where you establish your online publishing identity, in the boxes below you are giving this article a face and identity. In the world of the press release, its invaluable to have a solid reputation and credibility, this article has great information on creating author credibility in the form of Authorship.', 'newswire');
$newswire_config['tooltip']['company_info_tab']            = '';
$newswire_config['tooltip']['seo_tools_tab']               = __('Meta Keywords: Keywords are individual tags that google uses when searching for relevant content. These are not displayed in the search, however significant importance are placed in well chosen and relevant words. Examples of keywords for a dog care store in New York: Manhattan dog care, best dog car new york, dog grooming new york, best dog sitting manhattan. Note  that all keywords are seperated by commas. URL: This is the link title that will show at the end of your url, you can simply copy and paste your article title for this box. I.E. http://www.newswire.net/newsroom/local/76352-best-New-York-Dog-Care.html', 'newswire');
$newswire_config['tooltip']['pen_name']                    = __('You may choose a penname to appear as the author of your article.  It is strongly recommended, however,  that you consistantly use the same name, and that that name be the one linked to your Google+ author profile. ', 'newswire');
$newswire_config['tooltip']['contact_email']               = __('Please enter the best email for the editorial staff to contact you about approval, or problems with your article.  This email address will not be displayed, or used for any other purpose.', 'newswire');
$newswire_config['tooltip']['link_name']                   = __('Checking this box will create a hyperlink between your author name as displayed in the article, and the following URL.  The default URL that appears in this box, is the link your Profile in Newswire.  If you have completed your profile, with your Google+ profile DO NOT PUT YOUR GOOGLE+ PROFILE IN THIS BOX. Use this link box if you wish to link your profile to another source of personal information, such as a Facebook page, or personal blog.', 'newswire');
//company info tab
$newswire_config['tooltip']['company_info_include']        = __('By checking this box, the Newswire writer will place the following company information in the footer of your article, complete with proper Schema tags.', 'newswire');
$newswire_config['tooltip']['company_info_profile']        = __('Uncheck this box if you have not listed a business.  If you do not have a listed company and have this box checked you will not be able to publish the article. Otherwise check this box if you would like to use the same information you used in your registered company to fill out the company info form.', 'newswire');
$newswire_config['tooltip']['company_info_schema']         = __('Schema is a powerful kind of .html markup code.  To get the most SEO benefit from Schema, select the business category most applicable to your particular enterprise.  Not all business types are yet represented.  If you cant find a specific category that you think applies to your business, just use the default "Local Business".  For more more information on Schema markup, and to learn how to use it on your website, or in articles published elsewhere, visit : http://schema.org', 'newswire');
$newswire_config['tooltip']['company_info_contact_funnel'] = __('This is only availble for those with the enterprise subscription plan. If a reader registers with Newswire.net through your published article this option allows you to send them emails.', 'newswire');
//seo tools
$newswire_config['tooltip']['seo_tools_pub_language']      = __('Google indexes pages with different languages differently in order to better organize searches. To make your article more relevant to your target audience, pick the most appropriate language.', 'newswire');
$newswire_config['tooltip']['embed_media']                 = __('Embed Media Box.  Embedded media is most often a movie from Youtube.com, etc.  However, any HTML code can be inserted into this media box.  The information in this box will be displayed according to the “Style” selected.  This box is 300 pixels wide, and a maximum of 1200 high (lesser dimensions are allowable).  Always carefully preview your additions to the Embed Media Box prior to submission.  How this box appears in preview will be exactly how it appears to the readers of your article.', 'newswire');
//article
$newswire_config['tooltip']['article_description']         = __('Abstract/Meta Description” is a summary of the article that is indexed by search engines, and also displayed between the title and the article body in the published article.  This description is limited to 256 characters.  The abstract can be original, or cut from a portion of the article.  It should contain the key words and phrases that you wish your article to be indexed for', 'newswire');
$newswire_config['tooltip']['include_image'] = __('The main image you upload through this interface will be displayed in the Newsroom, and as a thumbnail on Newswire, and by Google News, and some of the other re-publishers of your release.  This image may or may not be appropriate for inclusion into the body of your article. This tick box allows you to select whether or not this image will be inserted into your article body.', 'newswire');

$newswire_config['tooltip']['img_alt_tag_link'] = __('Paste a Hyperlink in this box to link your image caption (text directly under image) to an outside website. This link is counted by Google against the total links on your page.  A good rule of thumb is to use no more than one link per 150 words in your artilce body.  If you use this linking tool, be sure to count this link in your total links.', 'newswire');

$newswire_config['tooltip']['freesites_submission'] = __('PressRoom will push you PR to 5 online news sites at no charge.  Your article will appear within 72 hours, if it meets the press release editorial guidelines of the site.  Each of these sites will notify you upon publication. To submit your press release to Google News, and even more syndication partners, upgrade to NewswireExpress', 'newswire');


$newswire_config['pressroom_metabox_tooltip']['pin_as_contact'] = __('Use the <i>New Contact Block</i> interface to make sharing contact information via your PressRoom™ easy.  The <i>Contact Block</i> interface helps you format your contact information with proper microdata.  When your contact info is downloaded and re-published by the journalists and bloggers who visit your site, you can be sure that search engines will recognize this data for what it is – names, addresses, and phone numbers, etc. rather than just random strings of data. Want more flexibility?  Use the html feature in the <i>Add Text Block</i> interface to create contacts formatted in any way imaginable.  Use the <i>All PressRoom Blocks</i> link from your dashboard to determine where your <i>Contact Block</i> appears on your PressRoom™ page. Your new <i>Contact Block</i> will also be available as a widget that can be placed anywhere you select on your website.', 'newswire');

$newswire_config['pressroom_metabox_tooltip']['pin_as_link'] = __('Use this easy interface to create lists of links, from text that you choose.  Consider making different <i>Link Blocks</i> for email addresses of company contacts, links to product reviews, or any other groups of URLs either inside or outside of your website that can help journalists and bloggers find the information that they need to better cover your company.  Want more flexibility?  Use the html view in the <i>Add Text Block</i> interface to create lists of links formatted in any way imaginable. You can set the order of all PressRoom™ blocks by dragging and dropping objects in the <i>All PressRoom Blocks</i> link from your dashboard. Your new <i>Link Block</i> will also be available as a widget that you can place anywhere else on your website.', 'newswire');

$newswire_config['pressroom_metabox_tooltip']['pin_as_social'] = __('Use this block to make it easy for visitors to your PressRoom™ to engage with your social networks. Beyond simple <i>Like</i>, <i>Tweet</i>, or <i>Google+</i> buttons, this interface allows you to embed your <i>Facebook Plugin</i>, <i>Google Badge</i>, <i>Twitter Timeline</i>, and much more into your PressRoom™.  This block works just like the <i>New Embed Block</i> interface, except that the embed code will not be displayed to your visitors.  To find embed codes for your social media accounts, go to the website of the provider (i.e. https://developers.facebook.com/docs/plugins/page-plugin) and create the embed code for the object you wish to use.  For a more complete guide, (<a href="http://newswire.net/embed_help">click here</a>)  As with all other PressRoom™ blocks, your social media blocks will become widgets that you can use elsewhere on your site.', 'newswire');

$newswire_config['pressroom_metabox_tooltip']['pin_as_quote'] = __('Use the <i>Add New Quote Block</i> interface to post quotes from key personnel onto your site in the correct format. Being quoted often and accurately is the objective of any public relation campaign. The <i>Quote Block</i> interface formats quotes from your principals correctly, and lets the quote be downloaded, including an attribution link, with one click.  Take control of the narrative about your company and its offerings by providing quotable content to the bloggers and journalists who visit your site.  Only visual mode is enabled in this interface.  All text entered will be centered, italicized, and bolded.  You can choose where your <i>Quote Block</i> will display on your PressRoom™ page by dragging and dropping the block on the <i>All PressRoom Blocks</i> link from your dashboard. Your new <i>Quote Block</i> block will also be available as a widget that you can place anywhere else on your website.','newswire');

$newswire_config['pressroom_metabox_tooltip']['pin_as_image'] = __('Simply drag and drop images from your computer, or select other images that you have already uploaded to your site, to populate a new <i>Image Album Block</i> for your PressRoom™.  You may find it helpful to create multiple image albums with a different kinds of content.  For example, you might want logos in one <i>Image Album</i>, product images in different album, and images of key employees in yet a different album. You can choose where your <i>Image Album Block</i> will display on your PressRoom™ page by dragging and dropping the blocks on the <i>All PressRoom Blocks</i> page accessed through the dashboard. Your new <i>Image Album block</i> will also be available as a widget for use elsewhere on your website from the dashboard at appearance>>widgets>>pressroom_blocks.', 'newswire');

$newswire_config['pressroom_metabox_tooltip']['pin_as_embed'] =__('The <i>Add Embed Block</i> interface lets you place videos, social media (Google+ badges, Facebook Plugins, Twitter Timelines, etc.) in your PressRoom™ so that both the content and the embed codes are displayed.  The embed code will be downloadable with one click by the bloggers and journalists that visit your page.  To find embed codes, go to the website of the provider (i.e. www.youtube.com) and find the embed code for the object you wish to use.  For a more complete guide, ( <a href="http://newswire.net/embed_help">click here</a> ) Once you have made your <i>Embed Block</i> select its order on the <i>All PressRoom Blocks</i> page by dragging and dropping the items in the order you wish them to appear. Your new <i>Embed Block</i> will also be available as a widget for use elsewhere on your website.', 'newswire');

$newswire_config['pressroom_metabox_tooltip']['pin_as_text'] =__('The Text Block is the most flexible of all of the PressRoom™ block creators.  Using the Add New Text Block interface, you can place anything you can imagine into your PressRoom™.  You can also use this interface to create any kind of widget content that you can think of.  These widgets can be used anywhere on your website. The Text Block creator works in either visual, or html mode, allowing both the casual user and the code expert to put anything that can be conceived into a PressRoom™ block. You can set the order of your new Text Block by dragging and dropping the object on the All PressRoom Blocks page.', 'newswire');

$newswire_config['pressroom_metabox_tooltip']['pr'] =__('The New Press Release interface has tool tips that will appear as pop-ups when you hover over the blue question marks.   Press releases will appear as a new page on your site.  Use the options found in the publish dialog box above to specify other places where your press release will appear.  By default, your press release will be included as a block on your PressRoom page.  You may also include your press release in the blog stream on your site.  If you have registered your Newswire API key, you may also choose the Publish option to syndicate your press release to outside websites at no charge.  Upgrade to <a href="http://newswire.net/download/newswirexpress"> NewswireXpress </a> for more publishing options, including Google News inclusion.', 'newswire');
