<?php

/*
 *
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

/**
 * view list of holidays
 */
class viewHolidayListAction extends baseLeaveAction {

    private $holidayService;
    private $leavePeriodService;
    private $workWeekEntity;

    /**
     * get Method for WorkWeekEntity
     *
     * @return WorkWeek $workWeekEntity
     */
    public function getWorkWeekEntity() {
        $this->workWeekEntity = new WorkWeek();
        return $this->workWeekEntity;
    }

    /**
     * Returns Leave Period
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {

        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }

        return $this->leavePeriodService;
    }

    /**
     * Returns Leave Period
     * @return LeavePeriodService
     */
    public function setLeavePeriodService($leavePeriodService) {
        $this->leavePeriodService = $leavePeriodService;
    }

    /**
     * get Method for Holiday Service
     *
     * @return HolidayService $holidayService
     */
    public function getHolidayService() {
        if (is_null($this->holidayService)) {
            $this->holidayService = new HolidayService();
        }
        return $this->holidayService;
    }

    /**
     * Set HolidayService
     * @param HolidayService $holidayService
     */
    public function setHolidayService(HolidayService $holidayService) {
        $this->holidayService = $holidayService;
    }

    /**
     * view Holiday list
     * @param sfWebRequest $request
     */
    public function execute($request) {
        //Keep Menu in Leave/Config 
        $request->setParameter('initialActionName', 'viewHolidayList');

        $this->holidayPermissions = $this->getDataGroupPermissions('holidays');

        $this->searchForm = $this->getSearchForm();

        $dateRange = $this->getLeavePeriodService()->getCalenderYearByDate(time());
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];

        $isPaging = $request->getParameter('pageNo');

        if ($isPaging > 0) {
            $pageNumber = $isPaging;
        } else {
            $pageNumber = 1;
        }

        $limit = 50;
        $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $limit) : ($request->getParameter('pageNo', 1) - 1) * $limit;

        if ($request->isMethod('post')) {

            $this->searchForm->bind($request->getParameter($this->searchForm->getName()));

            if ($this->searchForm->isValid()) {
                $values = $this->searchForm->getValues();

                $startDate = $values['calFromDate'];
                $endDate = $values['calToDate'];
                $subunit = $values['sub_unit'] == 0  ? null : $values['sub_unit'];
            }
        }

        $this->daysLenthList = WorkWeek::getDaysLengthList();
        $this->yesNoList = WorkWeek::getYesNoList();

        //pass subunit 
        $this->holidayList = $this->getHolidayService()->searchHolidays($startDate, $endDate, $subunit, $limit, $offset);

        if ($this->holidayPermissions->canRead()) {
            $this->setListComponent($this->holidayList, $this->holidayPermissions, $pageNumber, $limit);
        }
        $message = $this->getUser()->getFlash('templateMessage');
        $this->messageType = (isset($message[0])) ? strtolower($message[0]) : "";
        $this->message = (isset($message[1])) ? $message[1] : "";


        if ($this->getUser()->hasFlash('templateMessage')) {
            $this->templateMessage = $this->getUser()->getFlash('templateMessage');
            $this->getUser()->setFlash('templateMessage', array());
        }
    }

    protected function getSearchForm() {
        return new HolidayListSearchForm(array(), array(), true);
    }

    protected function setListComponent($holidayList, $permissions, $pageNumber, $limit){
        $runtimeDefinitions = array();
        $buttons = array();

        if ($permissions->canCreate()) {
            $buttons['Add'] = array('label' => 'Add');
        }

        if (!$permissions->canDelete()) {
            $runtimeDefinitions['hasSelectableRows'] = false;
        } else if ($permissions->canDelete()) {
            $buttons['Delete'] = array('label' => 'Delete',
                'type' => 'submit',
                'data-toggle' => 'modal',
                'data-target' => '#deleteConfModal',
                'class' => 'delete');
        }
        $runtimeDefinitions['buttons'] = $buttons;

        $configurationFactory = $this->getListConfigurationFactory();

        if ($permissions->canUpdate()) {
            $configurationFactory->setIsLinkable(true);
        } else {
            $configurationFactory->setIsLinkable(false);
        }
        
        $readOnlyIds = $this->getUnselectableHolidayIds();
        if (count($readOnlyIds) > 0) {
            $runtimeDefinitions['unselectableRowIds'] = $readOnlyIds;
        }        

        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setActivePlugin('orangehrmLeavePlugin');
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($holidayList[0]);
        ohrmListComponent::setPageNumber($pageNumber);
        $numRecords = count($holidayList[1]);
        ohrmListComponent::setItemsPerPage($limit);
        ohrmListComponent::setNumberOfRecords($numRecords);
    }

    protected function getListConfigurationFactory() {
        return new HolidayListConfigurationFactory();
    }
    
    protected function getUnselectableHolidayIds() {
        return array();
    }    

}
