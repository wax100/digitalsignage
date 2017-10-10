<?php
/**
     * Narrowcasting
     *
     * Copyright 2017 by Oene Tjeerd de Bruin <oenetjeerd@sterc.nl>
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

    switch($modx->event->name) {
        case 'OnHandleRequest':
            if ($modx->loadClass('Narrowcasting', $modx->getOption('narrowcasting.core_path', null, $modx->getOption('core_path').'components/narrowcasting/').'model/narrowcasting/', true, true)) {
                $narrowcasting = new Narrowcasting($modx);

                if ($narrowcasting instanceOf Narrowcasting) {
                    $narrowcasting->initializeContext($scriptProperties);
                }
            }

            break;
        case 'OnLoadWebDocument':
        case 'OnWebPagePrerender':
            if ($modx->loadClass('Narrowcasting', $modx->getOption('narrowcasting.core_path', null, $modx->getOption('core_path').'components/narrowcasting/').'model/narrowcasting/', true, true)) {
                $narrowcasting = new Narrowcasting($modx);

                if ($narrowcasting instanceOf Narrowcasting) {
                    $narrowcasting->initializePlayer($scriptProperties);
                }
            }

            break;
    }

    return;