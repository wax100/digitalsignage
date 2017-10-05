<?php

    $package = 'Narrowcasting';

    /* Custom settings */
    $settings = array(
        'user_name'         => array(
            'name'              => 'Name'
        ),
        'user_email'        => array(
            'name'              => 'Email address'
        ),
        'context'           => array(
            'xtype'             => 'modx-combo-context'
        ),
        'request_resource'  => array(),
        'export_resource'   => array(),
        'templates'         => array()
    );

    /* Permissions */
    $permissions = array(
        array(
            'name'          => 'narrowcasting',
            'description'   => 'To view the Narrowcasting package.',
            'value'         => 1,
            'templates'     => array('AdministratorTemplate'),
            'adminOnly'     => false
        ),
        array(
            'name'          => 'narrowcasting_settings',
            'description'   => 'To view/edit Narrowcasting package settings.',
            'value'         => 1,
            'templates'     => array('AdministratorTemplate'),
            'adminOnly'     => true
        )
    );

    /* Context */
    $contexts = array(
        'nc'                => array(
            'name'              => 'Narrowcasting',
            'settings'          => array(
                'base_url'          => array(
                    'value' 	        => '/nc/'
                ),
                'site_status'       => array(
                    'value' 	        => '1'
                ),
                'site_url'          => array(
                    'value' 	        => 'http://{http_host}/nc/'
                )
            )
        )
    );

    /* Resources */
    $resources = array(
        array(
            'pagetitle'     => 'Home',
            'content'       => '',
            'content_type'  => 7,
            'setting'       => 'request_resource'
        ),
        array(
            'pagetitle'     => 'Export',
            'content'       => '[[!Narrowcasting]]',
            'content_type'  => 7,
            'setting'       => 'export_resource'
        )
    );

    /* Templates */
    $templates = array(
        array(
            'name'          => 'Narrowcasting',
            'static'        => 1,
            'static_file'   => '/core/components/narrowcasting/elements/templates/narrowcasting.template.tpl',
            'source'        => 1,
            'icon'          => 'icon-play-circle'
        )
    );

    /* Slide types */
    $slides = array(
        'default'           => array(
            'time'              => 10,
            'data'              => 'a:2:{s:7:"content";a:4:{s:5:"xtype";s:8:"textarea";s:5:"value";s:0:"";s:5:"label";s:0:"";s:11:"description";s:0:"";}s:5:"image";a:4:{s:5:"xtype";s:18:"modx-combo-browser";s:5:"value";s:0:"";s:5:"label";s:0:"";s:11:"description";s:0:"";}}'
        ),
        'media'             => array(
            'time'              => '10',
            'icon'              => 'picture-o',
            'data'              => 'a:2:{s:7:"youtube";a:4:{s:5:"xtype";s:9:"textfield";s:5:"value";s:0:"";s:5:"label";s:0:"";s:11:"description";s:0:"";}s:10:"fullscreen";a:4:{s:5:"xtype";s:8:"checkbox";s:5:"value";s:1:"1";s:5:"label";s:0:"";s:11:"description";s:0:"";}}'
        ),
        'buienradar'        => array(
            'time'              => '10',
            'icon'              => 'cloud',
            'data'              => 'a:1:{s:8:"location";a:4:{s:5:"xtype";s:9:"textfield";s:5:"value";s:0:"";s:5:"label";s:0:"";s:11:"description";s:0:"";}}'
        )
    );

    $success = false;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            /* Get settings and sets the default value */
            foreach ($settings as $key => $setting) {
                $settings[$key] = array_merge(array(
                    'key'       => strtolower($package) . '.' . $key,
                    'namespace' => strtolower($package),
                    'area'      => strtolower($package)
                ));

                if (null !== ($settingObject = $object->xpdo->getObject('modSystemSetting', $settings[$key]['key']))) {
                    $settings[$key]['value'] = $settingObject->getValue();
                } else {
                    if (!isset($setting[$key]['value'])) {
                        $setting[$key]['value'] = '';
                    }
                }
            }

            /* Add permissions to the policy templates */
            foreach ($object->xpdo->getCollection('modAccessPolicyTemplate') as $templateObject) {
                foreach ($permissions as $permission) {
                    if (!isset($permission['templates']) || in_array($templateObject->get('name'), $permission['templates'])) {
                        $permission = array_merge($permission, array(
                            'template'  => $templateObject->get('id')
                        ));

                        $c = array(
                            'name'      => $permission['name'],
                            'template'  => $permission['template']
                        );

                        if (null === $object->xpdo->getObject('modAccessPermission', $c)) {
                            if (null !== ($permissionObject = $object->xpdo->newObject('modAccessPermission'))) {
                                $permissionObject->fromArray($permission);
                                $permissionObject->save();
                            }
                        }
                    }
                }
            }

            /* Add permissions to the policy */
            foreach ($object->xpdo->getCollection('modAccessPolicy') as $policyObject) {
                $data = $policyObject->get('data');

                foreach ($permissions as $permission) {
                    if ($permission['adminOnly']) {
                        if ('Administrator' == $policyObject->get('name')) {
                            $data[$permission['name']] = true;
                        } else {
                            $data[$permission['name']] = false;
                        }
                    } else {
                        $data[$permission['name']] = true;
                    }
                }

                $policyObject->set('data', $data);
                $policyObject->save();
            }

            /* Add contexts */
            foreach ($contexts as $key => $context) {
                $context = array_merge($context, array(
                    'key' => $key
                ));

                if (null === ($contextObject = $object->xpdo->getObject('modContext', $context['key']))) {
                    if (null !== ($contextObject = $object->xpdo->newObject('modContext'))) {
                        $contextObject->fromArray($context, '', true, true);
                        $contextObject->save();

                        if (isset($context['settings'])) {
                            foreach ($context['settings'] as $contextKey => $contextSetting) {
                                $contextSetting = array_merge(array(
                                    'context_key'   => $contextObject->get('key'),
                                    'key'           => $contextKey,
                                    'namespace'     => 'core',
                                    'area'          => 'core'
                                ), $contextSetting);

                                if (null !== ($contextSettingObject = $object->xpdo->newObject('modContextSetting'))) {
                                    $contextSettingObject->fromArray($contextSetting, '', true, true);
                                    $contextSettingObject->save();
                                }
                            }
                        }

                        if (isset($settings['context'])) {
                            $settings['context']['value'] = $contextObject->get('key');
                        }

                        $object->xpdo->log(xPDO::LOG_LEVEL_INFO, $context['name'] . ' context created.');
                    } else {
                        $object->xpdo->log(xPDO::LOG_LEVEL_ERROR, $context['name'] . ' context could not be created.');
                    }
                } else {
                    if (isset($settings['context'])) {
                        $settings['context']['value'] = $contextObject->get('key');
                    }

                    $object->xpdo->log(xPDO::LOG_LEVEL_INFO, $context['name'] . ' context allready exists.');
                }
            }

            /* Add resources */
            foreach ($resources as $key => $resource) {
                $resource = array_merge(array(
                    'context_key'   => $settings['context']['value'],
                    'published'     => 1,
                    'deleted'       => 0,
                    'hidemenu'      => 0,
                    'richtext'      => 0,
                    'template'      => '',
                    'menuindex'     => $key
                ), $resource);

                $c = array(
                    'context_key'   => $resource['context_key'],
                    'pagetitle'     => $resource['pagetitle']
                );

                if (null === ($resourceObject = $object->xpdo->getObject('modResource', $c))) {
                    if (null !== ($resourceObject = $object->xpdo->newObject('modResource'))) {
                        $resourceObject->fromArray($resource, '', true, true);
                        $resourceObject->save();

                        if (isset($resource['setting'])) {
                            if (isset($settings[$resource['setting']])) {
                                $settings[$resource['setting']]['value'] = $resourceObject->get('id');
                            }
                        }

                        $object->xpdo->log(xPDO::LOG_LEVEL_INFO, $resource['pagetitle'] . ' resource created.');
                    } else {
                        $object->xpdo->log(xPDO::LOG_LEVEL_ERROR, $resource['pagetitle'] . ' resource could not be created.');
                    }
                } else {
                    if (isset($resource['setting'])) {
                        if (isset($settings[$resource['setting']])) {
                            $settings[$resource['setting']]['value'] = $resourceObject->get('id');
                        }
                    }

                    $object->xpdo->log(xPDO::LOG_LEVEL_INFO, $resource['pagetitle'] . ' resource allready exists.');
                }
            }

            /* Add templates */
            foreach ($templates as $key => $template) {
                $template = array_merge($template, array(
                   'templatename' => $template['name']
                ));

                $c = array(
                    'templatename' => $template['templatename']
                );

                if (null === ($templateObject = $object->xpdo->getObject('modTemplate', $c))) {
                    if (null !== ($templateObject = $object->xpdo->newObject('modTemplate'))) {
                        $templateObject->fromArray($template, '', true, true);
                        $templateObject->save();

                        if (isset($settings['templates'])) {
                            if (isset($settings['templates']['value'])) {
                                $settings['templates']['value'] = implode(',', array_unique(array_filter(explode(',', $settings['templates']['value']) + array($templateObject->get('id')))));
                            } else {
                                $settings['templates']['value'] = $templateObject->get('id');
                            }
                        }

                        $object->xpdo->log(xPDO::LOG_LEVEL_INFO, $template['templatename'] . ' template created.');
                    } else {
                        $object->xpdo->log(xPDO::LOG_LEVEL_INFO, $template['templatename'] . ' template could not be created.');
                    }
                } else {
                    if (isset($settings['templates'])) {
                        if (isset($settings['templates']['value'])) {
                            $settings['templates']['value'] = implode(',', array_unique(array_filter(explode(',', $settings['templates']['value']) + array($templateObject->get('id')))));
                        } else {
                            $settings['templates']['value'] = $templateObject->get('id');
                        }
                    }

                    $object->xpdo->log(xPDO::LOG_LEVEL_INFO, $template['templatename'] . ' template allready exists.');
                }
            }

            /* Add customs settings */
            foreach ($settings as $key => $setting) {
                if (isset($options[$key])) {
                    $setting['value'] = $options[$key];
                }

                if (null === ($settingObject = $object->xpdo->getObject('modSystemSetting', $setting['key']))) {
                    if (null !== ($settingObject = $object->xpdo->newObject('modSystemSetting'))) {
                        $settingObject->fromArray($setting, '', true, true);
                        $settingObject->save();
                    } else {
                        $object->xpdo->log(xPDO::LOG_LEVEL_ERROR, $setting['key'] . ' setting could not be created.');
                    }
                } else {
                    $settingObject->fromArray($setting, '', true, true);
                    $settingObject->save();
                }
            }

            /* Add slides */
            if ($object->xpdo->loadClass('Narrowcasting', $object->xpdo->getOption('narrowcasting.core_path', null, $object->xpdo->getOption('core_path').'components/narrowcasting/').'model/narrowcasting/', true, true)) {
                $narrowcasting = new Narrowcasting($modx);

                foreach ($slides as $key => $slide) {
                    $slide = array_merge(array(
                        'key' => $key
                    ), $slide);

                    $c = array(
                        'key' => $slide['key']
                    );

                    if (null === ($slideObject = $object->xpdo->getObject('NarrowcastingSlidesTypes', $c))) {
                        if (null !== ($slideObject = $object->xpdo->newObject('NarrowcastingSlidesTypes'))) {
                            $slideObject->fromArray($slide, '', true, true);
                            $slideObject->save();

                            $object->xpdo->log(xPDO::LOG_LEVEL_INFO, $slide['key'] . ' slide type created.');
                        } else {
                            $object->xpdo->log(xPDO::LOG_LEVEL_ERROR, $slide['key'] . ' slide type could not be created.');
                        }
                    } else {
                        $object->xpdo->log(xPDO::LOG_LEVEL_INFO, $slide['key'] . ' slide type allready exists.');
                    }
                }
            }
            $success = true;

            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $success = true;

            break;
    }

    return $success;

?>