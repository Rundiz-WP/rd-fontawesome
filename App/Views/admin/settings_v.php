<div id="rd-fontawesome-form-result-placeholder"></div>

<form id="rd-fontawesome-settings-form" method="post">
    <div class="rd-fontawesome-tabs-container">
        <div class="rd-fontawesome-tabs">
            <a class="rd-fontawesome-tab active" href="#rd-fontawesome-tab-settings"><?php esc_html_e('Settings'); ?></a>
            <a class="rd-fontawesome-tab" href="#rd-fontawesome-tab-svinfo"><?php esc_html_e('Server info', 'rd-fontawesome'); ?></a>
        </div><!--.rd-fontawesome-tabs-->
        <div class="rd-fontawesome-tabs-content">
            <div id="rd-fontawesome-tab-settings">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><?php esc_html_e('Download type', 'rd-fontawesome'); ?></th>
                            <td>
                                <select id="rd-fontawesome-download_type" name="download_type">
                                    <option value="githubapi"<?php if (isset($settings['download_type']) && $settings['download_type'] === 'githubapi') {echo ' selected';} ?>><?php esc_attr_e('GitHub API', 'rd-fontawesome'); ?> (<?php esc_attr_e('Default', 'rd-fontawesome'); ?>)</option>
                                    <option value="github"<?php if (isset($settings['download_type']) && $settings['download_type'] === 'github') {echo ' selected';} ?>><?php esc_attr_e('GitHub', 'rd-fontawesome'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Latest Font Awesome version', 'rd-fontawesome'); ?></th>
                            <td>
                                <p id="rd-fontawesome-latestversion">-</p>
                                <button id="rd-fontawesome-retrieve-latestversion-btn" class="button" type="button"><?php esc_html_e('Retrieve latest version info.', 'rd-fontawesome'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Installed Font Awesome version', 'rd-fontawesome'); ?></th>
                            <td>
                                <p id="rd-fontawesome-currentversion"><?php echo ($settings['fontawesome_version'] ?? '-'); ?></p>
                                <button id="rd-fontawesome-install-latestversion-btn" class="button" type="button"><?php esc_html_e('Install latest version', 'rd-fontawesome'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Unload enqueued CSS handles', 'rd-fontawesome'); ?></th>
                            <td>
                                <input id="rd-fontawesome-dequeue-css" class="regular-text" type="text" name="dequeue_css" value="<?php esc_attr_e(($settings['dequeue_css'] ?? '')); ?>">
                                <p class="description">
                                    <?php esc_html_e('Dequeue the other Font Awesome CSS handles that was enqueued by other plugins or themes.', 'rd-fontawesome'); ?> 
                                    <?php 
                                    /* translators: %1$s comma sign. */
                                    printf(__('Separate values by %1$s.', 'rd-fontawesome'), '<code>,</code>'); 
                                    ?> 
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Unload enqueued JS handles', 'rd-fontawesome'); ?></th>
                            <td>
                                <input id="rd-fontawesome-dequeue-js" class="regular-text" type="text" name="dequeue_js" value="<?php esc_attr_e(($settings['dequeue_js'] ?? '')); ?>">
                                <p class="description">
                                    <?php esc_html_e('Dequeue the  other Font Awesome JavaScript handles that was enqueued by other plugins or themes.', 'rd-fontawesome'); ?> 
                                    <?php 
                                    /* translators: %1$s comma sign. */
                                    printf(__('Separate values by %1$s.', 'rd-fontawesome'), '<code>,</code>'); 
                                    ?> 
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="rd-fontawesome-dontenqueue"><?php esc_html_e('Do not enqueue assets', 'rd-fontawesome'); ?></label></th>
                            <td>
                                <input id="rd-fontawesome-dontenqueue" type="checkbox" name="donot_enqueue" value="1"<?php if (isset($settings['donot_enqueue']) && $settings['donot_enqueue'] === '1') {echo ' checked';} ?>>
                                <p class="description">
                                    <?php esc_html_e('Check this box to do not enqueue assets for this plugin.', 'rd-fontawesome'); ?> 
                                    <?php esc_html_e('The assets for this plugin such as CSS, fonts won\'t be loaded.', 'rd-fontawesome'); ?> 
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table><!--.form-table-->
                <p class="submit">
                    <button id="rd-fontawesome-settings-submit" class="button button-primary" type="submit"><?php esc_html_e('Save Changes'); ?></button>
                </p>
            </div><!--#rd-fontawesome-tab-settings-->
            <div id="rd-fontawesome-tab-svinfo">
                <table class="rd-fontawesome-svinfo-table">
                    <tbody>
                        <tr>
                            <th><?php esc_html_e('WordPress', 'rd-fontawesome'); ?>:</th>
                            <td><?php echo esc_html($serverinfo['wpVersion']); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('PHP', 'rd-fontawesome'); ?>:</th>
                            <td><?php echo esc_html(PHP_VERSION); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Execution timeout', 'rd-fontawesome'); ?>:</th>
                            <td><?php echo esc_html($serverinfo['phpExecTimeout']); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Memory limit', 'rd-fontawesome'); ?>:</th>
                            <td><?php echo esc_html($serverinfo['phpMemoryLimit']); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('WordPress Memory limit', 'rd-fontawesome'); ?>:</th>
                            <td><?php echo esc_html($serverinfo['wpMemoryLimit']); ?>
                                <p class="description"><code>WP_MAX_MEMORY_LIMIT</code></p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Plugin version', 'rd-fontawesome'); ?>:</th>
                            <td><?php echo esc_html(($serverinfo['pluginVersion'] ?? '?')); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Writable folders and files', 'rd-fontawesome'); ?>:</th>
                            <td><?php 
                            if (isset($serverinfo['writable'])) {
                                foreach ($serverinfo['writable'] as $path => $pathResult) {
                                    echo '<p>' . $path . '<br>';
                                    if ($pathResult === true) {
                                        esc_html_e('Yes', 'rd-fontawesome');
                                    } elseif ($pathResult === false) {
                                        echo '<span class="rd-fontawesome-txt-error">' . __('No', 'rd-fontawesome') . '</span>';
                                    } elseif ($pathResult === 'filenotexists') {
                                        echo '<span class="rd-fontawesome-txt-error">' . __('Not exists', 'rd-fontawesome') . '</span>';
                                    }
                                    echo '</p>' . PHP_EOL;
                                }// endforeach;
                                unset($pathResult, $path);
                            }
                            ?></td>
                        </tr>
                    </tbody>
                </table><!--.rd-fontawesome-svinfo-table-->
            </div><!--#rd-fontawesome-tab-svinfo-->
        </div><!--.rd-fontawesome-tabs-content-->
        
    </div><!--.rd-fontawesome-tabs-container-->
</form>