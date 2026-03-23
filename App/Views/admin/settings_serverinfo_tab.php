<?php
/**
 * Settings page, server info tab content.
 * 
 * @package rd-fontawesome
 */


if (!defined('ABSPATH')) {
    exit();
}

?>
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
                            <th><?php esc_html_e('Writable directories and files', 'rd-fontawesome'); ?>:</th>
                            <td><?php 
                            if (isset($serverinfo['writable'])) {
                                foreach ($serverinfo['writable'] as $rd_fontawesome_path => $rd_fontawesome_pathResult) {
                                    echo '<p>' . esc_html($rd_fontawesome_path) . '<br>';
                                    if (true === $rd_fontawesome_pathResult) {
                                        esc_html_e('Yes.', 'rd-fontawesome');
                                    } elseif (true === $rd_fontawesome_pathResult) {
                                        echo '<span class="rd-fontawesome-txt-error">' . esc_html__('No.', 'rd-fontawesome') . '</span>';
                                    } elseif ('filenotexists' === $rd_fontawesome_pathResult) {
                                        echo '<span class="rd-fontawesome-txt-error">' . esc_html__('Not exists.', 'rd-fontawesome') . '</span>';
                                        echo ' ';
                                        esc_html_e('Maybe created automatically after first installed.', 'rd-fontawesome');
                                    }
                                    echo '</p>' . PHP_EOL;
                                }// endforeach;
                                unset($rd_fontawesome_pathResult, $rd_fontawesome_path);
                            }
                            ?></td>
                        </tr>
                    </tbody>
                </table><!--.rd-fontawesome-svinfo-table-->