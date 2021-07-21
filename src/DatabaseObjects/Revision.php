<?php
namespace DatabaseObjects;

/****************************************************************************
 * Copyright (C) 2020, Sarah-Jane Whittaker (sarah@cs.queensu.ca)
 *
 * This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU Affero General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or (at 
 * your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero 
 * General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License 
 * along with this program in the file gnu-agpl-3.0.txt. If not, 
 * see <https://www.gnu.org/licenses/>.
 * 
 * This program was developed with the input and support of the Queen's
 * University School of Computing <https://www.cs.queensu.ca/>, Faculty
 * of Arts and Science <https://www.queensu.ca/artsci/> and the Office of 
 * the Vice-Provost (Teaching and Learning) 
 * <https://www.queensu.ca/provost/teaching-and-learning>.
 ***************************************************************************/


use Managers\CurriculumMappingDatabase as CMD;
use DatabaseObjects\DatabaseObject;

/**
 * The abstract class Revision represents a revision as stored in the database
 * <em>as well as</em> a bit of additional information to perform an
 * UPDATE database query.
 */
class Revision extends DatabaseObject {
    /************************************************************************
     * Static Functions
     ************************************************************************/
    /**
     * This function uses the values in a database row to create a new
     * Revision object and set the values of the member variables.
     *
     * @param $argArray        The course row from the database
     * @return                 A new Revision object with those values
     */
    public static function buildFromDBRow($argArray) {
        $id = $argArray[CMD::TABLE_REVISION_ID];
        $userID = $argArray[CMD::TABLE_REVISION_USER_ID];
        $revTable = $argArray[CMD::TABLE_REVISION_REV_TABLE];
        $revColumn = $argArray[CMD::TABLE_REVISION_REV_COLUMN];

        $keyColumns = explode(CMD::TABLE_REVISION_KEY_SEPARATOR, $argArray[CMD::TABLE_REVISION_KEY_COLUMNS]);
        $keyValues = explode(CMD::TABLE_REVISION_KEY_SEPARATOR, $argArray[CMD::TABLE_REVISION_KEY_VALUES]);

        $primaryKey = array();
        for ($i = 0; $i < count($keyColumns); $i++) {
            $primaryKey[$keyColumns[$i]] = $keyValues[$i];
        }

        $action = $argArray[CMD::TABLE_REVISION_ACTION];
        $priorValue = $argArray[CMD::TABLE_REVISION_PRIOR_VALUE];
        $dateAndTime = $argArray[CMD::TABLE_REVISION_DATE_AND_TIME];

        return new Revision($id, $userID, $revTable, $revColumn, $primaryKey, $action, $priorValue, $dateAndTime);
    }
    
    /**
     * 
     * @return type
     */
    public static function getSortFunction() {
        return function($a, $b) { 
                return (strtotime($a->getDateAndTime()) > strtotime($b->getDateAndTime()));
            };        
    }     

    
    /************************************************************************
     * Member Variables
     ************************************************************************/
    protected $userID = null;
    protected $revTable = null;
    protected $revColumn = null;
    protected $primaryKey = null;
    protected $action = null;
    protected $priorValue = null;
    protected $currentValue = null;
    protected $dateAndTime = null;

    
    /************************************************************************
     * Constructor
     ************************************************************************/
    /**
     * This constructor sets all of the member variables except for the parent
     * ID, which may be left as null/unset/empty.
     *
     * @param $argDBID          The revision's database integer ID
     * @param $argUserID        The revision's string 'number'
     * @param $argRevTable      The revision's table name
     * @param $argRevColumn     The revision's column name
     * @param $argPrimaryKey    An array of the revision's primary key
     * @param $argAction        The revision's action
     * @param $argPriorValue    The revision's prior value
     * @param $argDateAndTime   The revision's date and time
     * @param $argCurrentValue  The revision's current value (default of null)
     */
    public function __construct($argDBID, $argUserID, $argRevTable, 
        $argRevColumn, $argPrimaryKey, $argAction, $argPriorValue, 
        $argDateAndTime, $argCurrentValue = null) {
        parent::__construct($argDBID);

        $this->userID = $argUserID;

        $this->revTable = $argRevTable;
        $this->revColumn = $argRevColumn;

        $this->primaryKey = $argPrimaryKey;
        $this->action = $argAction;

        if ($argPriorValue !== null) {
            $this->priorValue = $argPriorValue;
        }

        $this->dateAndTime = $argDateAndTime;

        if ($argCurrentValue !== null) {
            $this->currentValue = $argCurrentValue;
        }
    }

    
    /************************************************************************
     * Get and Set Methods
     ************************************************************************/
    /**
     * The get method for the revision's user ID.
     *
     * @return  The string ID
     */
    public function getUserID() {
        return $this->userID;
    }

    /**
     * The get method for the name of the revision's table.
     *
     * @return  The string name of the table
     */
    public function getTable() {
        return $this->revTable;
    }

    /**
     * The get method for the name of the revision's column.
     *
     * @return  The string name of the column
     */
    public function getColumn() {
        return $this->revColumn;
    }

    /**
     * The get method for the name of the revision's primary key.
     *
     * @return  An array of column/value pairs
     */
    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    /**
     * The get method for the revision's action.
     *
     * @return  The string action
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * The get method for the revision's prior value.
     *
     * @return  The string prior value
     */
    public function getPriorValue() {
        return $this->priorValue;
    }

    /**
     * The get method for the revision's date and time.
     *
     * @return  The string date and time
     */
    public function getDateAndTime() {
        return $this->dateAndTime;
    }

    /**
     * The get method for the revision's current value.
     *
     * @return  The string current value
     */
    public function getCurrentValue() {
        return $this->currentValue;
    }

    
    /************************************************************************
     * Member Functions
     ************************************************************************/
    /**
     * Determines whether this revision has the current value set.
     *
     * @return      A boolean representing whether the current value is set
     */
    public function hasCurrentValue() {
        return (!empty($this->currentValue));
    }

    /**
     * Determines whether this revision represents an edit to an existing
     * row in the database.
     *
     * @return     A boolean with whe the the action is an edit (true) or
     *             not (false)
     */
    public function isEdit() {
        return $this->action == CMD::TABLE_REVISION_ACTION_EDITED;
    }

    /**
     * Determines whether this revision represents the deletion of an existing
     * row in the database.
     *
     * @return     A boolean with whe the the action is a delete (true) or
     *             not (false)
     */
    public function isDelete() {
        return $this->action == CMD::TABLE_REVISION_ACTION_DELETED;
    }

    /**
     * Determines whether this revision represents the addition of a new
     * row in the database.
     *
     * @return     A boolean with whe the the action is an add (true) or
     *             not (false)
     */
    public function isAdd() {
        return $this->action == CMD::TABLE_REVISION_ACTION_ADDED;
    }

    /**
     * 
     * @return type
     */
    public function getPrimaryKeyColumns() {
        return array_keys($this->primaryKey);
    }

    /**
     * 
     * @return type
     */
    public function getPrimaryKeyValues() {
        return array_values($this->primaryKey);
    }

    /**
     * 
     * @return type
     */
    public function getPrimaryKeyQueryClause() {
        $queryClauseArray = array();

        foreach ($this->primaryKey as $key => $value) {
            $queryClauseArray[] = "$key = ?";
        }

        return join(" AND ", $queryClauseArray);
    }

    /**
     * Creates a string of the columns in the primary key. It's intended for
     * storage in the database.
     *
     * @return     A string of column names separated by
     *             CMD::TABLE_REVISION_KEY_SEPARATOR
     */
    public function getPrimaryKeyColumnString() {
        return join(CMD::TABLE_REVISION_KEY_SEPARATOR, array_keys($this->primaryKey));
    }

    /**
     * Creates a string of the values in the primary key. It's intended for
     * storage in the database.
     *
     * @return     A string of column values separated by
     *             CMD::TABLE_REVISION_KEY_SEPARATOR
     */
    public function getPrimaryKeyValueString() {
        return join(CMD::TABLE_REVISION_KEY_SEPARATOR, array_values($this->primaryKey));
    }

    
    /************************************************************************
     * Member Functions
     ************************************************************************/
    /**
     * 
     * @param type $db_curriculum
     * @return string
     */
    public function getComponentNameAndLink($db_curriculum) {
        $firstElement = null;
        $firstFallback = null;
        $secondElement = null;
        $secondFallback = null;
        $name = "";
        $link = "";
        $result = "";

        switch ($this->revTable) {
            case CMD::TABLE_CLLO:
                $firstElement = $db_curriculum->getCLLOFromID(
                        $this->primaryKey[CMD::TABLE_CLLO_ID]);
                $firstFallback = "CLLO (ID " . $this->primaryKey[
                        CMD::TABLE_CLLO_ID] . ")";
                break;
            case CMD::TABLE_PLLO:
                $firstElement = $db_curriculum->getPLLOFromID(
                        $this->primaryKey[CMD::TABLE_PLLO_ID]);
                $firstFallback = "PLLO (ID " . $this->primaryKey[
                        CMD::TABLE_PLLO_ID] . ")";
                break;
            case CMD::TABLE_CLLO_AND_COURSE_AND_LEVEL:
                $firstElement = $db_curriculum->getCLLOFromID(
                        $this->primaryKey[CMD::TABLE_CLLO_AND_COURSE_AND_LEVEL_CLLO_ID]);
                $firstFallback = "CLLO (ID " . $this->primaryKey[
                        CMD::TABLE_CLLO_AND_COURSE_AND_LEVEL_CLLO_ID] . ")";
                $secondElement = $db_curriculum->getCourseFromID(
                        $this->primaryKey[CMD::TABLE_CLLO_AND_COURSE_AND_LEVEL_COURSE_ID]);
                $secondFallback = "Course (ID " . $this->primaryKey[
                        CMD::TABLE_CLLO_AND_COURSE_AND_LEVEL_COURSE_ID] . ")";
                break;
            case CMD::TABLE_CLLO_AND_PLLO:
                $firstElement = $db_curriculum->getCLLOFromID(
                        $this->primaryKey[CMD::TABLE_CLLO_AND_PLLO_CLLO_ID]);
                $firstFallback = "CLLO (ID " . $this->primaryKey[
                        CMD::TABLE_CLLO_AND_PLLO_CLLO_ID] . ")";
                $secondElement = $db_curriculum->getPLLOFromID(
                        $this->primaryKey[CMD::TABLE_CLLO_AND_PLLO_PLLO_ID]);
                $secondFallback = "PLLO (ID " . $this->primaryKey[
                        CMD::TABLE_CLLO_AND_PLLO_PLLO_ID] . ")";
                break;
            case CMD::TABLE_CLLO_AND_ILO:
                $firstElement = $db_curriculum->getCLLOFromID(
                        $this->primaryKey[CMD::TABLE_CLLO_AND_ILO_CLLO_ID]);
                $firstFallback = "CLLO (ID " . $this->primaryKey[
                        CMD::TABLE_CLLO_AND_ILO_CLLO_ID] . ")";
                $secondElement = $db_curriculum->getILOFromID(
                        $this->primaryKey[CMD::TABLE_CLLO_AND_ILO_ILO_ID]);
                $secondFallback = "ILO (ID " . $this->primaryKey[
                        CMD::TABLE_CLLO_AND_ILO_ILO_ID] . ")";
                break;
            case CMD::TABLE_PLLO_AND_DLE:
                $firstElement = $db_curriculum->getPLLOFromID(
                        $this->primaryKey[CMD::TABLE_PLLO_AND_DLE_PLLO_ID]);
                $firstFallback = "PLLO (ID " . $this->primaryKey[
                        CMD::TABLE_PLLO_AND_DLE_PLLO_ID] . ")";
                $secondElement = $db_curriculum->getDLEFromID(
                        $this->primaryKey[CMD::TABLE_PLLO_AND_DLE_DLE_ID]);
                $secondFallback = "DLE (ID " . $this->primaryKey[
                        CMD::TABLE_PLLO_AND_DLE_DLE_ID] . ")";
                break;
            case CMD::TABLE_PLLO_AND_ILO:
                $firstElement = $db_curriculum->getPLLOFromID(
                        $this->primaryKey[CMD::TABLE_PLLO_AND_ILO_PLLO_ID]);
                $firstFallback = "PLLO (ID " . $this->primaryKey[
                        CMD::TABLE_PLLO_AND_ILO_PLLO_ID] . ")";
                $secondElement = $db_curriculum->getILOFromID(
                        $this->primaryKey[CMD::TABLE_PLLO_AND_ILO_ILO_ID]);
                $secondFallback = "ILO (ID " . $this->primaryKey[
                        CMD::TABLE_PLLO_AND_ILO_ILO_ID] . ")";
                break;
            case CMD::TABLE_PLAN_AND_PLLO:
                $firstElement = $db_curriculum->getPLLOFromID(
                        $this->primaryKey[CMD::TABLE_PLAN_AND_PLLO_PLLO_ID]);
                $firstFallback = "PLLO (ID " . $this->primaryKey[
                        CMD::TABLE_PLAN_AND_PLLO_PLLO_ID] . ")";
                $secondElement = $db_curriculum->getPlanFromID(
                        $this->primaryKey[CMD::TABLE_PLAN_AND_PLLO_PLAN_ID]);
                $secondFallback = "Plan (ID " . $this->primaryKey[
                        CMD::TABLE_PLAN_AND_PLLO_PLAN_ID] . ")";
                break;
            default:
                break;
        }

        if ($firstElement) {
            $name = $firstElement->getName();
            $link = $firstElement->getLinkToView();
            $result = "<a href=\"$link\">$name</a>";
        } else if ($firstFallback) {
            $result = $firstFallback;
        }

        if ($secondElement) {
            $name = $secondElement->getName();
            $link = $secondElement->getLinkToView();
            $result .= ' <i class="fas fa-arrow-right" aria-hidden="true" title="linked to"></i> <span class="sr-only">linked to</span> <a href="'.$link.'">'.$name.'</a>';
        } else if ($secondFallback) {
            $result .= ' <i class="fas fa-arrow-right" aria-hidden="true" title="linked to"></i> <span class="sr-only">linked to</span>'.$secondFallback;
        }

        return $result;
    }

    /**
     * 
     * @return string
     */
    public function getPropertyName() {
        switch ($this->revTable . $this->revColumn) {
            case null:
            case "" :
                return QSC_CMP_HTML_REVISION_NONE;
            case CMD::TABLE_CLLO_AND_COURSE_AND_LEVEL . CMD::TABLE_CLLO_AND_COURSE_AND_LEVEL_LEVEL_ID :
                return "Level";
            case CMD::TABLE_CLLO . CMD::TABLE_CLLO_IOA :
                return "Indicator of Achievement";
            case CMD::TABLE_CLLO . CMD::TABLE_CLLO_PARENT_ID :
                return "Parent CLLO";
            case CMD::TABLE_PLLO . CMD::TABLE_PLLO_PARENT_ID :
                return "Parent PLLO";
            case CMD::TABLE_DLE . CMD::TABLE_DLE_PARENT_ID :
                return "Parent DLE";
            case CMD::TABLE_ILO . CMD::TABLE_ILO_PARENT_ID;
                return "Parent ILO";
            default:
                return ucfirst($this->revColumn);
        }
    }

    /**
     * 
     * @param type $db_curriculum
     * @return type
     */
    public function getPriorValueName($db_curriculum) {
        $element = null;
        $fallback = null;

        if (!$this->priorValue) {
            return QSC_CMP_HTML_REVISION_NONE;
        }

        switch ($this->revTable . $this->revColumn) {
            case CMD::TABLE_CLLO . CMD::TABLE_CLLO_PARENT_ID :
                $element = $db_curriculum->getCLLOFromID($this->priorValue);
                $fallback = "CLLO (ID " . $this->priorValue . ")";
                break;
            case CMD::TABLE_PLLO . CMD::TABLE_PLLO_PARENT_ID :
                $element = $db_curriculum->getPLLOFromID($this->priorValue);
                $fallback = "PLLO (ID " . $this->priorValue . ")";
                break;
            case CMD::TABLE_DLE . CMD::TABLE_DLE_PARENT_ID :
                $element = $db_curriculum->getDLEFromID($this->priorValue);
                $fallback = "DLE (ID " . $this->priorValue . ")";
                break;
            case CMD::TABLE_ILO . CMD::TABLE_ILO_PARENT_ID;
                $element = $db_curriculum->getILOFromID($this->priorValue);
                $fallback = "ILO (ID " . $this->priorValue . ")";
                break;
            default:
                return $this->priorValue;
        }

        if (!$element) {
            return $fallback ? $fallback : QSC_CMP_HTML_REVISION_NONE;
        }

        $name = $element->getName();
        $link = $element->getLinkToView();
        return "<a href=\"$link\">$name</a>";
    }
    
    /**
     * 
     * @return type
     */
    public function getName() {
        $text_name = ucfirst($this->action);
        $text_name .= " | ";
        switch ($this->revTable) {
            case CMD::TABLE_CLLO:
                $text_name .= "CLLO"; break;
            case CMD::TABLE_PLLO:
                $text_name .= "PLLO"; break;
            case CMD::TABLE_DLE:
                $text_name .= "DLE"; break;
            case CMD::TABLE_ILO;
                $text_name .= "ILO"; break;
            default:
                break;
        }
        
        if ($this->priorValue) {
            $text_name .= " | ";
            $text_name .= $this->priorValue;
        }
        
        return $text_name;        
    }

}
