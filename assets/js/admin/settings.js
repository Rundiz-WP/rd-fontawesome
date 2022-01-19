/* 
 * Admin Settings page.
 * 
 * @pakcage rd-fontawesome
 * @license http://opensource.org/licenses/MIT MIT
 * @since 1.0.0
 */


class RdFontAwesomeSettings {


    /**
     * Detect current active tab target ID.
     * 
     * @returns {null|string} Return `null` or current active tab target ID.
     */
    detectActiveTab() {
        let tabsNav = document.querySelectorAll('.rd-fontawesome-tab');
        let currentActiveTabId = null;

        if (tabsNav) {
            for (let i = 0; i < tabsNav.length; i++) {
                let item = tabsNav[i];
                if (item.classList.contains('active')) {
                    if (item.hash) {
                        currentActiveTabId = item.hash;
                        break;
                    }
                }
            }
        }

        return currentActiveTabId;
    }// detectActiveTab


    /**
     * Initialize the class.
     * 
     * @returns {undefined}
     */
    init() {
        // tabs work.
        let activeTabId = this.detectActiveTab();
        this.setActiveTabContent(activeTabId);
        this.listenClickTab();

        this.retrieveLatestVersion();
        this.installLatestVersion();

        this.listenFormSubmit();
    }// init


    /**
     * Download and install latest version.
     * 
     * @returns {undefined}
     */
    installLatestVersion() {
        let thisClass = this;
        let installedVersionElement = document.getElementById('rd-fontawesome-currentversion');
        let latestVersionElement = document.getElementById('rd-fontawesome-latestversion');
        let installBtn = document.getElementById('rd-fontawesome-install-latestversion-btn');
        let formResultPlaceholder = document.getElementById('rd-fontawesome-form-result-placeholder');
        let rdfontawesomeLoading = false;

        if (installBtn) {
            installBtn.addEventListener('click', (e) => {
                e.preventDefault();

                let ajaxdata = {
                    'action': 'rdfontawesome_installlatestversion',
                    'nonce': RdFontAwesomeSettingsObject.nonce,
                    'download_type': document.getElementById('rd-fontawesome-download_type').value
                };

                if (false === rdfontawesomeLoading) {
                    installedVersionElement.innerHTML = RdFontAwesomeSettingsObject.txtLoading;
                    formResultPlaceholder.innerHTML = '';
                    rdfontawesomeLoading = true;

                    jQuery.ajax({
                        'url': ajaxurl,
                        'method': 'post',
                        'data': ajaxdata
                    })
                    .done((data, textStatus, jqXHR) => {
                        if (data && data.tagVersion) {
                            let previewHTML = data.tagVersion;
                            installedVersionElement.innerHTML = previewHTML;

                            let previewLatestVerHTML = '<a href="' + data.downloadLink + '" target="_blank">' + data.tagVersion + '</a>';
                            latestVersionElement.innerHTML = previewLatestVerHTML;
                        } else {
                            installedVersionElement.innerHTML = '-';
                        }

                        if (data && data.formResultMessage) {
                            let alertBox = '<div class="notice notice-success is-dismissible">';
                            for (let i = 0; i < data.formResultMessage.length; i++) {
                                alertBox += '<p>' + data.formResultMessage[i] + '</p>';
                            }
                            alertBox += '<button class="notice-dismiss" type="button" onclick="return jQuery(this).parent().remove();"><span class="screen-reader-text">' + RdFontAwesomeSettingsObject.txtDismissNotice + '</span></button>';
                            alertBox += '</div>';
                            formResultPlaceholder.innerHTML = alertBox;
                        }
                    })
                    .fail((jqXHR, textStatus, errorThrown) => {
                        let response;
                        if (jqXHR && jqXHR.responseJSON) {
                            response = jqXHR.responseJSON;
                        }

                        installedVersionElement.innerHTML = '-';

                        if (response && response.formResultMessage) {
                            let alertBox = '<div class="notice notice-error is-dismissible">';
                            for (let i = 0; i < response.formResultMessage.length; i++) {
                                alertBox += '<p>' + response.formResultMessage[i] + '</p>';
                            }
                            alertBox += '<button class="notice-dismiss" type="button" onclick="return jQuery(this).parent().remove();"><span class="screen-reader-text">' + RdFontAwesomeSettingsObject.txtDismissNotice + '</span></button>';
                            alertBox += '</div>';
                            formResultPlaceholder.innerHTML = alertBox;
                        }
                    })
                    .always((data, textStatus, jqXHR) => {
                        rdfontawesomeLoading = false;
                    });
                }
            });// end click event listener.
        }// endif install button
    }// installLatestVersion


    /**
     * Listen click on tab nav and set active tab content.
     * 
     * @returns {undefined}
     */
    listenClickTab() {
        let thisClass = this;
        let tabsNav = document.querySelectorAll('.rd-fontawesome-tab');

        if (tabsNav) {
            tabsNav.forEach((item, index) => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    let thisElement = e.target;
                    if (thisElement.hash) {
                        thisClass.setActiveTabNav(thisElement);
                        thisClass.setActiveTabContent(thisElement.hash);
                    }
                });
            });
        }
    }// listenClickTab


    /**
     * Listen form submit and make AJAX save.
     * 
     * @returns {undefined}
     */
    listenFormSubmit() {
        let thisClass = this;
        let thisForm = document.getElementById('rd-fontawesome-settings-form');
        let formResultPlaceholder = document.getElementById('rd-fontawesome-form-result-placeholder');
        let rdfontawesomeLoading = false;

        if (thisForm) {
            thisForm.addEventListener('submit', (e) => {
                e.preventDefault();

                let formData = new FormData(thisForm);
                formData.append('action', 'rdfontawesome_savesettings');
                formData.append('nonce', RdFontAwesomeSettingsObject.nonce);

                if (false === rdfontawesomeLoading) {
                    rdfontawesomeLoading = true;
                    formResultPlaceholder.innerHTML = '';

                    jQuery.ajax({
                        'url': ajaxurl,
                        'method': 'post',
                        'data': new URLSearchParams(formData).toString()
                    })
                    .done((data, textStatus, jqXHR) => {
                        if (data && data.formResultMessage) {
                            let alertBox = '<div class="notice notice-success is-dismissible">';
                            for (let i = 0; i < data.formResultMessage.length; i++) {
                                alertBox += '<p>' + data.formResultMessage[i] + '</p>';
                            }
                            alertBox += '<button class="notice-dismiss" type="button" onclick="return jQuery(this).parent().remove();"><span class="screen-reader-text">' + RdFontAwesomeSettingsObject.txtDismissNotice + '</span></button>';
                            alertBox += '</div>';
                            formResultPlaceholder.innerHTML = alertBox;
                        }
                    })
                    .fail((jqXHR, textStatus, errorThrown) => {
                        let response;
                        if (jqXHR && jqXHR.responseJSON) {
                            response = jqXHR.responseJSON;
                        }

                        if (response && response.formResultMessage) {
                            let alertBox = '<div class="notice notice-error is-dismissible">';
                            for (let i = 0; i < response.formResultMessage.length; i++) {
                                alertBox += '<p>' + response.formResultMessage[i] + '</p>';
                            }
                            alertBox += '<button class="notice-dismiss" type="button" onclick="return jQuery(this).parent().remove();"><span class="screen-reader-text">' + RdFontAwesomeSettingsObject.txtDismissNotice + '</span></button>';
                            alertBox += '</div>';
                            formResultPlaceholder.innerHTML = alertBox;
                        }
                    })
                    .always((data, textStatus, jqXHR) => {
                        rdfontawesomeLoading = false;
                    });
                }
            });
        } else {
            console.error('Form is not exists.');
        }
    }// listenFormSubmit


    /**
     * Retrieve latest version.
     * 
     * @returns {undefined}
     */
    retrieveLatestVersion() {
        let retrieveBtn = document.getElementById('rd-fontawesome-retrieve-latestversion-btn');
        let latestVersionElement = document.getElementById('rd-fontawesome-latestversion');
        let rdfontawesomeLoading = false;

        if (retrieveBtn) {
            retrieveBtn.addEventListener('click', (e) => {
                e.preventDefault();

                let ajaxdata = {
                    'action': 'rdfontawesome_retrievelatestversion',
                    'nonce': RdFontAwesomeSettingsObject.nonce,
                    'download_type': document.getElementById('rd-fontawesome-download_type').value
                };

                if (false === rdfontawesomeLoading) {
                    latestVersionElement.innerHTML = RdFontAwesomeSettingsObject.txtLoading;
                    rdfontawesomeLoading = true;

                    jQuery.ajax({
                        'url': ajaxurl,
                        'method': 'get',
                        'data': ajaxdata
                    })
                    .done((data, textStatus, jqXHR) => {
                        if (data && data.downloadLink && data.tagVersion) {
                            let previewHTML = '<a href="' + data.downloadLink + '" target="_blank">' + data.tagVersion + '</a>';
                            latestVersionElement.innerHTML = previewHTML;
                        } else {
                            latestVersionElement.innerHTML = '-';
                        }
                    })
                    .fail((jqXHR, textStatus, errorThrown) => {
                        latestVersionElement.innerHTML = '-';
                    })
                    .always((data, textStatus, jqXHR) => {
                        rdfontawesomeLoading = false;
                    });
                }
            });// end click event listener.
        }// endif retrieve button
    }// retrieveLatestVersion


    /**
     * Set active to selected tab content.
     * 
     * @param {string} selector
     * @returns {undefined}
     */
    setActiveTabContent(selector) {
        if (selector === null) {
            return ;
        }

        // remove all active tab content.
        document.querySelectorAll('.rd-fontawesome-tabs-content > *').forEach((item, index) => {
            item.classList.remove('active');
        });

        // set active tab content.
        let tabContent = document.querySelector(selector);
        if (tabContent) {
            tabContent.classList.add('active');
        }
    }// setActiveTabContent


    /**
     * Set active tab nav.
     * 
     * @param {object} HTMLElement
     * @returns {undefined}
     */
    setActiveTabNav(HTMLElement) {
        if (typeof(HTMLElement) === 'object') {
            // remove all active tab content.
            document.querySelectorAll('.rd-fontawesome-tab').forEach((item, index) => {
                item.classList.remove('active');
            });

            // set active tab nav.
            HTMLElement.classList.add('active');
        }
    }// setActiveTabNav


}// RdFontAwesomeSettings


document.addEventListener('DOMContentLoaded', () => {
    let settings = new RdFontAwesomeSettings();
    
    settings.init();
});