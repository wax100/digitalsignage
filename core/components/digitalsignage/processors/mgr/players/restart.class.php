<?php

    class DigitalSignagePlayersRestartProcessor extends modObjectUpdateProcessor {
        /**
         * @access public.
         * @var String.
         */
        public $classKey = 'DigitalSignagePlayers';

        /**
         * @access public.
         * @var Array.
         */
        public $languageTopics = array('digitalsignage:default');

        /**
         * @access public.
         * @var String.
         */
        public $objectType = 'digitalsignage.players';

        /**
         * @access public.
         * @var Object.
         */
        public $digitalsignage;

        /**
         * @acces public.
         * @return Mixed.
         */
        public function initialize() {
            $this->digitalsignage = $this->modx->getService('digitalsignage', 'DigitalSignage', $this->modx->getOption('digitalsignage.core_path', null, $this->modx->getOption('core_path').'components/digitalsignage/').'model/digitalsignage/');

            return parent::initialize();
        }

        /**
         * @access public.
         * @return Mixed.
         */
        public function beforeSave() {
            $this->object->set('restart', 0 == $this->object->restart ? 1 : 0);

            return parent::beforeSave();
        }
    }

    return 'DigitalSignagePlayersRestartProcessor';

?>