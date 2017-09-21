<?php
	
	/**
	 * Narrowcasting
	 *
	 * Copyright 2016 by Oene Tjeerd de Bruin <oenetjeerd@sterc.nl>
	 *
	 * Narrowcasting is free software; you can redistribute it and/or modify it under
	 * the terms of the GNU General Public License as published by the Free Software
	 * Foundation; either version 2 of the License, or (at your option) any later
	 * version.
	 *
	 * Narrowcasting is distributed in the hope that it will be useful, but WITHOUT ANY
	 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
	 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License along with
	 * Narrowcasting; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
	 * Suite 330, Boston, MA 02111-1307 USA
	 */

	class NarrowcastingSlideTypesDataUpdateProcessor extends modObjectUpdateProcessor {
        /**
         * @access public.
         * @var String.
         */
        public $classKey = 'NarrowcastingSlidesTypes';

        /**
         * @access public.
         * @var Array.
         */
        public $languageTopics = array('narrowcasting:default');

        /**
         * @access public.
         * @var String.
         */
        public $objectType = 'narrowcasting.slidestypes';

        /**
         * @access public.
         * @var Object.
         */
        public $narrowcasting;

        /**
         * @access public.
         * @return Mixed.
         */
        public function initialize() {
            $this->narrowcasting = $this->modx->getService('narrowcasting', 'Narrowcasting', $this->modx->getOption('narrowcasting.core_path', null, $this->modx->getOption('core_path').'components/narrowcasting/').'model/narrowcasting/');

            if (null !== ($key = $this->getProperty('key'))) {
                $this->setProperty('key', strtolower(str_replace(array(' ', '-'), '_', $key)));
            }

            return parent::initialize();
        }

        /**
         * @access public.
         * @return Mixed.
         */
        public function process() {
            $criteria = array(
                'key' => $this->getProperty('key')
            );

            if (null !== ($object = $this->modx->getObject($this->classKey, $this->getProperty('id')))) {
                if (!preg_match('/^([a-zA-Z0-9\_\-]+)$/si', $this->getProperty('key'))) {
                    $this->addFieldError('key', $this->modx->lexicon('narrowcasting.error_slide_type_data_character'));
                } else {
                    if (null === ($data = unserialize($object->data))) {
                        $data = array();
                    }

                    $object->fromArray(array(
                        'data' => serialize(array_merge($data, array(
                            $this->getProperty('key') => array(
                                'xtype'      => $this->getProperty('xtype'),
                                'value'      => $this->getProperty('value'),
                                'inputValue' => $this->getProperty('value')
                            )
                        )))
                    ));

                    if (!$object->save()) {
                        $this->addFieldError('key', $this->modx->lexicon('narrowcasting.error_slide_type_not_exists'));
                    } else {
                        return $this->success('', $object);
                    }
                }
            } else {
                $this->addFieldError('key', $this->modx->lexicon('narrowcasting.error_slide_type_not_exists'));
            }

            return $this->failure();
        }
	}
	
	return 'NarrowcastingSlideTypesDataUpdateProcessor';
	
?>