<?php

use Cake\Core\Configure;

echo $this->element('genericElements/IndexTable/index_table', [
    'data' => [
        'data' => $data,
        'top_bar' => [
            'children' => [
                [
                    'children' => [
                        [
                            'class' => 'd-none mass-select',
                            'text' => __('Disable selected users'),
                            'onClick' => "multiSelectToggleField",
                            'onClickParams' => ['admin/users', 'massToggleField', 'disabled', '1', '#UserUserIds']
                        ],
                        [
                            'class' => 'd-none mass-select',
                            'text' => __('Enable selected users'),
                            'onClick' => "multiSelectToggleField",
                            'onClickParams' => ['admin/users', 'massToggleField', 'disabled', '0', '#UserUserIds']
                        ],
                        [
                            'class' => 'd-none mass-select',
                            'text' => __('Disable publish emailing'),
                            'onClick' => "multiSelectToggleField",
                            'onClickParams' => ['admin/users', 'massToggleField', 'autoalert', '0', '#UserUserIds']
                        ],
                        [
                            'class' => 'd-none mass-select',
                            'text' => __('Enable publish emailing'),
                            'onClick' => "multiSelectToggleField",
                            'onClickParams' => ['admin/users', 'massToggleField', 'autoalert', '1', '#UserUserIds']
                        ],
                    ]
                ],
                [
                    'type' => 'simple',
                    'children' => [
                        'data' => [
                            'id' => 'create-button',
                            'title' => __('Modify filters'),
                            'fa-icon' => 'search',
                            'onClick' => 'getPopup',
                            'onClickParams' => [$urlparams, 'users', 'filterUserIndex']
                            /*
                            'type' => 'simple',
                            'text' => __('Add User'),
                            'class' => 'btn btn-primary',
                            'popover_url' => '/users/add'
                            */
                        ]
                    ]
                ],
                [
                    'type' => 'simple',
                    'children' => [
                        [
                            'url' => $baseurl . '/admin/users/index',
                            'text' => __('All'),
                            'active' => !isset($passedArgsArray['disabled']),
                        ],
                        [
                            'url' => $baseurl . '/admin/users/index/searchdisabled:0',
                            'text' => __('Active'),
                            'active' => isset($passedArgsArray['disabled']) && $passedArgsArray['disabled'] === "0",
                        ],
                        [
                            'url' => $baseurl . '/admin/users/index/searchdisabled:1',
                            'text' => __('Disabled'),
                            'active' => isset($passedArgsArray['disabled']) && $passedArgsArray['disabled'] === "1",
                        ]
                    ]
                ],
                [
                    'type' => 'search',
                    'button' => __('Search'),
                    'placeholder' => __('Enter value to search'),
                    'data' => '',
                    'searchKey' => 'value'
                ],
                [
                    'type' => 'table_action',
                ],
            ]
        ],
        'fields' => [
            [
                'element' => 'selector',
                'class' => 'short',
                'data' => [
                    'id' => [
                        'value_path' => 'id'
                    ]
                ]
            ],
            [
                'name' => __('ID'),
                'sort' => 'id',
                'class' => 'short',
                'data_path' => 'id'
            ],
            [
                'name' => __('Org'),
                'sort' => 'org_id',
                'element' => 'org',
                'data_path' => 'Organisation'
            ],
            [
                'name' => __('Role'),
                'sort' => 'role_id',
                'class' => 'short',
                'element' => 'role',
                'data_path' => 'Role'
            ],
            [
                'name' => __('Email'),
                'sort' => 'email',
                'data_path' => 'email'
            ],
            [
                'name' => '',
                'header_title' => __('Contact alert'),
                'icon' => 'handshake',
                'element' => 'boolean',
                'sort' => 'contactalert',
                'class' => 'short',
                'data_path' => 'contactalert',
                'colors' => true,
            ],
            [
                'name' => '',
                'header_title' => __('Notification'),
                'sort' => 'id',
                'data_path' => 'id',
                'icon' => 'clock',
                'element' => 'function',
                'class' => 'short',
                'function' => function (\Cake\Datasource\EntityInterface $user) use ($periodic_notifications) {
                    $subscriptions = [];
                    if ($user['autoalert']) {
                        $subscriptions[] = 'e';
                    }
                    foreach ($periodic_notifications as $period) {
                        if (!empty($user['User'][$period])) {
                            $subscriptions[] = substr($period, 13, 1);
                        }
                    }
                    return implode('/', $subscriptions);
                }
            ],
            [
                'name' => '',
                'header_title' => __('PGP public key'),
                'icon' => 'key',
                'element' => 'boolean',
                'sort' => 'gpgkey',
                'class' => 'short',
                'data_path' => 'gpgkey',
                'colors' => true,
            ],
            [
                'name' => '',
                'header_title' => __('S/MIME public key'),
                'icon' => 'lock',
                'element' => 'boolean',
                'sort' => 'certif_public',
                'class' => 'short',
                'data_path' => 'certif_public',
                'requirement' => Configure::read('SMIME.enabled')
            ],
            [
                'name' => __('SID'),
                'sort' => 'nids_sid',
                'class' => 'short',
                'data_path' => 'nids_sid'
            ],
            [
                'name' => '',
                'header_title' => __('Terms accepted'),
                'icon' => 'gavel',
                'element' => 'boolean',
                'sort' => 'termsaccepted',
                'class' => 'short',
                'data_path' => 'termsaccepted',
                'colors' => true,
            ],
            [
                'name' => __('Last Login'),
                'sort' => 'current_login',
                'element' => 'datetime',
                'empty' => __('Never'),
                'class' => 'short',
                'data_path' => 'current_login'
            ],
            [
                'name' => __('Created'),
                'sort' => 'date_created',
                'element' => 'datetime',
                'class' => 'short',
                'data_path' => 'date_created'
            ],
            [
                'name' => '',
                'header_title' => __('Monitored'),
                'icon' => 'desktop',
                'element' => 'toggle',
                'url' => $baseurl . '/admin/users/monitor',
                'url_params_vars' => [
                    [
                        'datapath' => [
                            'id'
                        ]
                    ]
                ],
                'sort' => 'monitored',
                'class' => 'short',
                'data_path' => 'monitored',
                /*'requirement' => $isSiteAdmin && Configure::read('Security.user_monitoring_enabled')*/
            ],
            [
                'name' => __('Last API Access'),
                'sort' => 'last_api_access',
                'element' => 'datetime',
                'class' => 'short',
                'data_path' => 'last_api_access',
                'requirement' => !empty(Configure::read('MISP.store_api_access_time')),
            ],
            [
                'name' => (Configure::read('Plugin.CustomAuth_name') ? Configure::read('Plugin.CustomAuth_name') : __('External Auth')),
                'sort' => 'external_auth_required',
                'element' => 'boolean',
                'class' => 'short',
                'data_path' => 'external_auth_required',
                'requirement' => Configure::read('Plugin.CustomAuth_enable') && empty(Configure::read('Plugin.CustomAuth_required'))
            ],
            [
                'name' => '',
                'header_title' => __('Monitored'),
                'icon' => 'desktop',
                'element' => 'toggle',
                'url' => $baseurl . '/admin/users/monitor',
                'url_params_data_paths' => array(
                    'id'
                ),
                'sort' => 'monitored',
                'class' => 'short',
                'data_path' => 'monitored',
                'requirement' => $isSiteAdmin && Configure::read('Security.user_monitoring_enabled')
            ],
            [
                'name' => '',
                'header_title' => __('User disabled'),
                'icon' => 'times',
                'element' => 'boolean',
                'sort' => 'disabled',
                'class' => 'short',
                'data_path' => 'disabled',
                'colors' => true,
            ],
            [
                'name' => __('Disabled'),
                'sort' => 'disabled',
                'data_path' => 'disabled',
                'element' => 'toggle',
                'url' => '/users/toggle/{{0}}',
                'url_params_vars' => ['id'],
                'toggle_data' => [
                    'editRequirement' => [
                        'function' => function($row, $options) {
                            return true;
                        },
                    ],
                    'skip_full_reload' => true
                ]
            ]
        ],
        'title' => __('User index'),
        'description' => __('The list of enrolled users in this Cerebrate instance. All of the users have or at one point had access to the system.'),
        'pull' => 'right',
        'actions' => [
            [
                'url' => '/users/view',
                'url_params_data_paths' => ['id'],
                'icon' => 'eye'
            ],
            [
                'open_modal' => '/users/edit/[onclick_params_data_path]',
                'modal_params_data_path' => 'id',
                'icon' => 'edit',
                'complex_requirement' => [
                    'options' => [
                        'datapath' => [
                            'role_id' => 'role_id'
                        ]
                    ],
                    'function' => function ($row, $options)  use ($loggedUser, $validRoles) {
                        if (empty($loggedUser['role']['perm_admin'])) {
                            if (empty($loggedUser['role']['perm_org_admin'])) {
                                return false;
                            }
                            if (!isset($validRoles[$options['datapath']['role_id']])) {
                                return false;
                            }
                        }
                        return true;
                    }
                ]
            ],
            [
                'open_modal' => '/users/delete/[onclick_params_data_path]',
                'modal_params_data_path' => 'id',
                'icon' => 'trash',
                'complex_requirement' => [
                    'options' => [
                        'datapath' => [
                            'role_id' => 'role_id'
                        ]
                    ],
                    'function' => function ($row, $options)  use ($loggedUser, $validRoles) {
                        if (empty($loggedUser['role']['perm_admin'])) {
                            if (empty($loggedUser['role']['perm_org_admin'])) {
                                return false;
                            }
                            if (!isset($validRoles[$options['datapath']['role_id']])) {
                                return false;
                            }
                        }
                        return true;
                    }
                ]
            ],
        ]
    ]
]);
echo '</div>';
?>
