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


/******************************************************************************
 * Constants
 *****************************************************************************/
const QSC_CMP_FORM_CLLO_ADD = "#form-cllo-add";
const QSC_CMP_FORM_CLLO_EDIT = "#form-cllo-edit";
const QSC_CMP_FORM_CLLO_DELETE = "#form-cllo-delete";

const QSC_CMP_FORM_CLLO_ID = "#cllo-id";
const QSC_CMP_FORM_CLLO_COURSE_INPUT = "#cllo-course-input";
const QSC_CMP_FORM_CLLO_COURSE_SELECT = "#cllo-course-select";
const QSC_CMP_FORM_CLLO_NUMBER = "#cllo-number";
const QSC_CMP_FORM_CLLO_PARENT_SELECT = "#cllo-parent-select";
const QSC_CMP_FORM_CLLO_PARENT_UNSELECT = "#cllo-parent-unselect";
const QSC_CMP_FORM_CLLO_TEXT = "#cllo-text";
const QSC_CMP_FORM_CLLO_TYPE = "#cllo-type";
const QSC_CMP_FORM_CLLO_IOA = "#cllo-ioa";
const QSC_CMP_FORM_CLLO_NOTES = "#cllo-notes";

const QSC_CMP_FORM_CLLO_PLLO_LIST_POSSIBLE = "#cllo-pllo-list-possible";
const QSC_CMP_FORM_CLLO_PLLO_LIST_SUPPORTED = "#cllo-pllo-list-supported";
const QSC_CMP_FORM_CLLO_PLLO_ADD = "#cllo-pllo-add";
const QSC_CMP_FORM_CLLO_PLLO_REMOVE = "#cllo-pllo-remove";

const QSC_CMP_FORM_CLLO_ILO_LIST_POSSIBLE = "#cllo-ilo-list-possible";
const QSC_CMP_FORM_CLLO_ILO_LIST_SUPPORTED = "#cllo-ilo-list-supported";
const QSC_CMP_FORM_CLLO_ILO_ADD = "#cllo-ilo-add";
const QSC_CMP_FORM_CLLO_ILO_REMOVE = "#cllo-ilo-remove"; 


/******************************************************************************
 * Functions
 *****************************************************************************/
function qscCMPCLLOHandleCourseInput() {
    // Get the current value that the user's entered in the input box
    let currentCourseValue = $(this).val();

    // Prep the AJAX data
    let ajaxData = {action: QSC_CMP_AJAX_ACTION_SEARCH_COURSES,
        search: currentCourseValue
    };

    // Get the select box that goes with the input box and remove all
    // the current options
    let courseSelect = $(QSC_CMP_FORM_CLLO_COURSE_SELECT);
    courseSelect.find("option").remove();

    qscCorePerformAJAXRequest(QSC_CMP_AJAX_SCRIPT_GET_COURSES, ajaxData,
        function (jsonData) {
            // Create new options from the JSON data and put them in the select
            courseSelect.append(qscCoreCreateOptionsFromJSONData(jsonData, 'id', 'name'));

            // Did the update eliminate the previous selection? If so, remove
            // all options from the CLLO list except "None".
            let selectedCourse = courseSelect.find("option:selected");
            if (!selectedCourse.length) {
                $(QSC_CMP_FORM_CLLO_PARENT_SELECT).find("option").remove();
            }
        }
    );    
}

function qscCMPCLLOHandleCourseSelection() {
    // Get the current CLLO ID and the course selection box
    let currentCLLOID = $(QSC_CMP_FORM_CLLO_ID).val();
    let courseSelect = $(QSC_CMP_FORM_CLLO_COURSE_SELECT);

    // Any change to the course selection means removing the prior
    // parent CLLO options
    let parentCLLOSelect = $(QSC_CMP_FORM_CLLO_PARENT_SELECT);
    parentCLLOSelect.find("option").remove();

    // Get the selected option from the list of courses and check that
    // the change was to select, not unselect
    let selectedCourse = courseSelect.find("option:selected");
    if (! selectedCourse.length) {
        return;
    }

    // Prep the AJAX data
    let ajaxData = {action: QSC_CMP_AJAX_ACTION_GET_CLLOS_FOR_COURSE,
        id: selectedCourse.val()
    };
    
    qscCorePerformAJAXRequest(QSC_CMP_AJAX_SCRIPT_GET_CLLOS, ajaxData,
        function (jsonData) {
            // Create new options from the JSON data and put them in the select
            parentCLLOSelect.append(
                qscCoreCreateOptionsFromJSONData(jsonData, 'id', 'name', currentCLLOID));
        }
    );
}


/******************************************************************************
 * Document - Ready
 *****************************************************************************/
$(document).ready(function() {
    // Handle the user editing the selected course
    $(QSC_CMP_FORM_CLLO_COURSE_INPUT).keyup(qscCMPCLLOHandleCourseInput);
    
    // Handle the user changing the selected course, which must change
    // the possible parent CLLO options
    $(QSC_CMP_FORM_CLLO_COURSE_SELECT).change(qscCMPCLLOHandleCourseSelection);    
    
    // Handle the user selecting the '>>' and '<<' buttons to move PLLOs
    // and ILOs back and forth
    qscCoreTransferOptionOnClick(QSC_CMP_FORM_CLLO_PLLO_ADD,
        QSC_CMP_FORM_CLLO_PLLO_LIST_POSSIBLE, 
        QSC_CMP_FORM_CLLO_PLLO_LIST_SUPPORTED);
    qscCoreTransferOptionOnClick(QSC_CMP_FORM_CLLO_PLLO_REMOVE,
        QSC_CMP_FORM_CLLO_PLLO_LIST_SUPPORTED, 
        QSC_CMP_FORM_CLLO_PLLO_LIST_POSSIBLE);
    qscCoreTransferOptionOnClick(QSC_CMP_FORM_CLLO_ILO_ADD,
        QSC_CMP_FORM_CLLO_ILO_LIST_POSSIBLE, 
        QSC_CMP_FORM_CLLO_ILO_LIST_SUPPORTED);
    qscCoreTransferOptionOnClick(QSC_CMP_FORM_CLLO_ILO_REMOVE,
        QSC_CMP_FORM_CLLO_ILO_LIST_SUPPORTED, 
        QSC_CMP_FORM_CLLO_ILO_LIST_POSSIBLE);
    
    // Handle the user 'unselecting' the parent CLLO because Ctrl + click
    // doesn't unselect an option in a single select box
    qscCoreUnselectWithButton(QSC_CMP_FORM_CLLO_PARENT_UNSELECT,
        QSC_CMP_FORM_CLLO_PARENT_SELECT);    
    
    // Handle form submission for add and edit
    $(QSC_CMP_FORM_CLLO_ADD + ', ' + QSC_CMP_FORM_CLLO_EDIT).submit(function(event) {
        // Select all of the options in the supported PLLOs and ILOs lists
        // so they appear in $_POST
        qscCoreSelectAllOptions(QSC_CMP_FORM_CLLO_PLLO_LIST_SUPPORTED);
        qscCoreSelectAllOptions(QSC_CMP_FORM_CLLO_ILO_LIST_SUPPORTED);
                
        // Set the flag that JS is working
        qscCMPSetJSCompletedOnSubmission(); 
    });
    
    // Handle form submission for delete
    $(QSC_CMP_FORM_CLLO_DELETE).submit(function() {
        return qscCoreConfirmDelete("CLLO");
    });    
});