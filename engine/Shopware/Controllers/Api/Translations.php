<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

class Shopware_Controllers_Api_Translations extends Shopware_Controllers_Api_Rest
{
    /**
     * @var \Shopware\Components\Api\Resource\Translation
     */
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('translation');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        // We still support the old behavior
        $request = $this->Request();
        $localeId = $request->getPost('localeId');

        if ($localeId !== null) {
            $request->setPost('shopId', $localeId);
            $request->setPost('localeId', null);
        }
    }

    /**
     * Get list of translations
     *
     * GET /api/translations/
     */
    public function indexAction()
    {
        $limit  = $this->Request()->getParam('limit', 1000);
        $offset = $this->Request()->getParam('start', 0);
        $sort   = $this->Request()->getParam('sort', array());
        $filter = $this->Request()->getParam('filter', array());

        $result = $this->resource->getList($offset, $limit, $filter, $sort);

        $this->View()->assign($result);
        $this->View()->assign('success', true);
    }

    /**
     * Create new translation
     *
     * POST /api/translations
     */
    public function postAction()
    {
        $useNumberAsId = (boolean) $this->Request()->getParam('useNumberAsId', 0);
        $params = $this->Request()->getPost();

        if ($useNumberAsId) {
            $translation = $this->resource->createByNumber($params);
        } else {
            $translation = $this->resource->create($params);
        }

        $location = $this->apiBaseUrl . 'translations/' . $translation->getId();
        $data = array(
            'id'       => $translation->getId(),
            'location' => $location
        );

        $this->View()->assign(array('success' => true, 'data' => $data));
        $this->Response()->setHeader('Location', $location);
    }

    /**
     * Update translation
     *
     * PUT /api/translations/{id}
     */
    public function putAction()
    {
        $useNumberAsId = (boolean) $this->Request()->getParam('useNumberAsId', 0);

        $id = $this->Request()->getParam('id');
        $params = $this->Request()->getPost();

        if ($useNumberAsId) {
            $translation = $this->resource->updateByNumber($id, $params);
        } else {
            $translation = $this->resource->update($id, $params);
        }

        $location = $this->apiBaseUrl . 'translations/' . $translation->getId();
        $data = array(
            'id'       => $translation->getId(),
            'location' => $location
        );

        $this->View()->assign(array('success' => true, 'data' => $data));
    }

    /**
     * Delete translation
     *
     * DELETE /api/translation/{id}
     */
    public function deleteAction()
    {
        $id = $this->Request()->getParam('id');
        $data = $this->Request()->getParams();
        $useNumberAsId = (boolean) $this->Request()->getParam('useNumberAsId', 0);

        if ($useNumberAsId) {
            $this->resource->deleteByNumber($id, $data);
        } else {
            $this->resource->delete($id, $data);
        }

        $this->View()->assign(array('success' => true));
    }
}
