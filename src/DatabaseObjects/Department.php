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

/**
 * The class Department is intended to store the information from a row in the
 * database about a department.
 */
class Department extends CalendarComponent {
    /*************************************************************************
     * Static Functions
     *************************************************************************/
    /**
     * This function uses the values in a database row to create a new 
     * Department object and set the values of the member variables.
     *
     * @param $argArray        The department row from the database
     */
    public static function buildFromDBRow($argArray) {
        $id = $argArray[CMD::TABLE_DEPARTMENT_ID];
        $name = $argArray[CMD::TABLE_DEPARTMENT_NAME];

        return new Department($id, $name);
    }
    

    /*************************************************************************
     * Constructor
     *************************************************************************/
    /**
     * This constructor sets all of the member variables using the arguments.
     *
     * @param $argDBID         The department's database integer ID
     * @param $argName       The department's string name
     */
    public function __construct($argDBID, $argName) {
        parent::__construct($argDBID, $argName);
    }
    

    /*************************************************************************
     * Member Functions
     *************************************************************************/
    /**
     * Creates a link to view this department using its ID.
     *
     * @return      A string containing the link
     */
    public function getLinkToView() {
        return self::getLinkWithID(QSC_CMP_DEPARTMENT_VIEW_PAGE_LINK);
    }

}
