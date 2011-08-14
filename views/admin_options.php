<div class="wrap">
    <h2>Fancy Sitemap</h2>
    <div id="fancySitemapAdmin">
        <fieldset>
            <legend>Shortcode</legend>
            copy and paste <b>[fancy-sitemap]</b> to the content area of a page to display your sitemap.<br />
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="DW4CAJSDCXKQ2">
                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" style="vertical-align:middle;" />
                <span>to Support My Drinking Habit</span>
                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1" />
            </form>
            <small>This plugin is created using <a href="http://raphaeljs.com" target="_blank">Raphaël—JavaScript Library</a></small>
        </fieldset>
        <form method="post" action="<?php echo $url;?>">
            <fieldset class="displayMode <?php echo $type!='page'?'closed':'';?>">
                <legend>
                    <label>Sitemap by Pages <input type="radio" name="type" value="page" <?php echo $type=='page'?'checked="checked"':'';?> /></label>
                </legend>
                <small>
                    Generate sitemap automatically using your wordpress Pages
                </small>
                <div class="formRow">
                    <label for="page_exclude"><strong>Exlucde</strong> these pages:</label>
                    
                    <select id="page_exclude" name="page_exclude[]" multiple="multiple" size="15">
                        <?php fancy_sitemap_output_page_children(0,0,$pageExcludes);?>
                    </select>
                    <br />
                    <a href="#" class="selectAll">Select All</a> | 
                    <a href="#" class="selectNone">Select None</a> | 
                    <a href="#" class="selectInverse">Select Inverse</a>
                    <small>hold ctrl and click to select multiple</small>
                </div>
                <div class="formRow">
                    <input type="submit" value="Save" class="button" />
                </div>
            </fieldset>
            <fieldset class="displayMode <?php echo $type!='menu'?'closed':'';?>">
                <legend>
                    <label>Sitemap by Menu <input type="radio" name="type" value="menu" <?php echo $type=='menu'?'checked="checked"':'';?> /></label>
                </legend>
                <small>
                    Generate sitemap by outputting menus
                </small>
                <div class="formRow">
                    <label for="page_exclude"><strong>Include</strong> these menu:</label>
                    
                    <select id="page_exclude" name="menus[]" multiple="multiple" size="5">
                        <?php fancy_sitemap_output_menu($menus, $menuIncludes);?>
                    </select>
                    <br />
                    <a href="#" class="selectAll">Select All</a> | 
                    <a href="#" class="selectNone">Select None</a> | 
                    <a href="#" class="selectInverse">Select Inverse</a>
                    <small>hold ctrl and click to select multiple</small>
                </div>
                <div class="formRow">
                    <input type="submit" value="Save" class="button" />
                </div>
            </fieldset>
            <fieldset>
                <legend>Options</legend>
                <div class="formRow">
                    <label for="bbc">Auto Block Width:</label>
                    <input type="hidden" name="options[auto_size]" value="0" /> 
                    <input type="checkbox" name="options[auto_size]" size="1" id="autoSize" value="1" <?php echo $options['auto_size']=='1'?'checked="checked"':'';?> />
                </div>
                <div class="formRow blockSize">
                    <label for="bbc">Block Size:</label>
                    <input type="text" name="options[width]" class="width" size="1" value="<?php echo !empty($options['width'])?$options['width']:'60';?>" /> X
                    <input type="text" name="options[height]" size="1" value="<?php echo !empty($options['height'])?$options['height']:'40';?>" />
                    <?php if($options['auto_size']=='1'){ ?>
                    <input type="hidden" name="options[width]" value="<?php echo !empty($options['width'])?$options['width']:'60';?>" />
                    <?php }?>
                </div>
                <div class="formRow">
                    <label for="bbc">Block Border Color:</label>
                    <input type="text" class="fsColorpicker" id="bbc" name="options[border_color]" value="<?php echo isset($options['border_color'])?$options['border_color']:'#000000';?>" />
                </div>
                <div class="formRow">
                    <label for="bbgc">Block Background Color:</label>
                    <input type="text" class="fsColorpicker" id="bbgc" name="options[background_color]" value="<?php echo isset($options['background_color'])?$options['background_color']:'#ffffff';?>" />
                </div>
                <div class="formRow">
                    <label for="bbgc">Block Background hover Color:</label>
                    <input type="text" class="fsColorpicker" id="bbgvc" name="options[background_hover_color]" value="<?php echo isset($options['background_hover_color'])?$options['background_hover_color']:'#ffffff';?>" />
                </div>
                <div class="formRow">
                    <label for="lc">Line Color:</label>
                    <input type="text" class="fsColorpicker" id="lc" name="options[line_color]" value="<?php echo isset($options['line_color'])?$options['line_color']:'#000000';?>" />
                </div>
                <div class="formRow">
                    <label for="lc">Line Width:</label>
                    <input type="text" name="options[line_width]" value="<?php echo isset($options['line_width'])?$options['line_width']:'1';?>" />
                </div>
                <div class="formRow">
                    <label for="bbc">Font Size:</label>
                    <input type="text" name="options[font_size]" size="1" value="<?php echo isset($options['font_size'])?$options['font_size']:'8';?>" />
                    (<input type="text" name="options[font_size_hover]" size="1" value="<?php echo isset($options['font_size_hover'])?$options['font_size_hover']:'22';?>" /> while hover)
                </div>
                <div class="formRow">
                    <label for="lc">Font Color:</label>
                    <input type="text" class="fsColorpicker" id="lc" name="options[font_color]" value="<?php echo isset($options['font_color'])?$options['font_color']:'#000000';?>" />
                </div>
                <div class="formRow">
                    <label for="lc">Use Home as Root:</label>
                    <input type="hidden"name="options[use_home]" value="0" />
                    <input type="checkbox"name="options[use_home]" value="1" <?php echo isset($options['use_home']) && $options['use_home'] == '1'?'checked="checked"':'';?> />
                </div>
                <div class="formRow">
                    <label for="lc">Home Text(if above is checked):</label>
                    <input type="text" name="options[home_text]" value="<?php echo isset($options['home_text'])?$options['home_text']:'Home';?>" />
                </div>
                <div class="formRow">
                    <input type="submit" value="Save" class="button" />
                </div>
            </fieldset>
            <fieldset>
                <legend>Preview</legend>
                <small>Use this to preview and position your sitemap.<br />Admin section and frontend should have a similar width so it looks the same on both.</small>
                <div class="formRow">
                    <div id="sitemapHolder">
                        <?php echo fancy_sitemap_get_output(true);?>
                    </div>
                    <div class="formRow">
                        <input type="submit" value="Save" class="button" id="savePosition" />
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>