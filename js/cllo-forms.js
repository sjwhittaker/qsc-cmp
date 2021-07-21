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
const QSC_CMP_FORM_CLLO_COURSE_LIST_POSSIBLE = "#cllo-course-list-possible";
const QSC_CMP_FORM_CLLO_LEVEL_LIST_POSSIBLE = "#cllo-level-list-possible";
const QSC_CMP_FORM_CLLO_COURSE_AND_LEVEL_ADD = "#cllo-course-and-level-add";
const QSC_CMP_FORM_CLLO_COURSE_AND_LEVEL_REMOVE = "#cllo-course-and-level-remove";
const QSC_CMP_FORM_CLLO_COURSE_LIST_SELECTED = "#cllo-course-list-selected";
const QSC_CMP_FORM_CLLO_LEVEL_LIST_SELECTED = "#cllo-level-list-selected";

const QSC_CMP_FORM_CLLO_PARENT_COURSE_LIST = "#cllo-parent-course-list";
const QSC_CMP_FORM_CLLO_PARENT_COURSE_LIST_HELP = "#cllo-parent-course-list-help";
const QSC_CMP_FORM_CLLO_PARENT_COURSE_CLLO_LIST = "#cllo-parent-course-cllo-list";
const QSC_CMP_FORM_CLLO_PARENT_COURSE_CLLO_LIST_HELP = "#cllo-parent-course-cllo-list-help";
const QSC_CMP_FORM_CLLO_PARENT_COURSE_CLLO_UNSELECT = "#cllo-parent-course-cllo-unselect";

const QSC_CMP_FORM_CLLO_NUMBER = "#cllo-number";

const QSC_CMP_FORM_CLLO_TEXT = "#cllo-text";
const QSC_CMP_FORM_CLLO_TYPE = "#cllo-type";
const QSC_CMP_FORM_CLLO_IOA = "#cllo-ioa";
const QSC_CMP_FORM_CLLO_NOTES = "#cllo-notes";

const QSC_CMP_FORM_CLLO_PLLO_INPUT = "#cllo-pllo-list-input";
const QSC_CMP_FORM_CLLO_PLLO_INPUT_HELP = "#cllo-pllo-list-input-help";
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
function qscCMPGetSelectedCoursesIDArray() {
    // Create an array of ID numbers from the current list of selected 
    // courses to exclude them
    let selectCoursesIDArray = [];
    $(QSC_CMP_FORM_CLLO_COURSE_LIST_SELECTED + " option").each(function() {
        selectCoursesIDArray.push(parseInt($(this).val()));
    });
    
    return selectCoursesIDArray;
}


function qscCMPCLLOHandleCourseInput() {
    // Get the current value that the user's entered in the input box and
    // the set of currently selected courses
    let currentCourseValue = $(this).val();    
    let selectCoursesIDArray = qscCMPGetSelectedCoursesIDArray();
    
    // Prep the AJAX data
    let ajaxData = {action: QSC_CMP_AJAX_ACTION_SEARCH_COURSES,
        search: currentCourseValue,
        exclude: selectCoursesIDArray
    };

    // Get the select box that goes with the input box and remove all
    // the current options
    let courseSelect = $(QSC_CMP_FORM_CLLO_COURSE_LIST_POSSIBLE);
    courseSelect.find("option").remove();

    qscCorePerformAJAXRequest(QSC_CMP_AJAX_SCRIPT_GET_COURSES, ajaxData,
        function (jsonData) {
            // Create new options from the JSON data and put them in the select
            courseSelect.append(qscCoreCreateOptionsFromJSONData(jsonData, 'id', 'name'));

            // Did the update eliminate the previous selection? If so, remove
            // all options from the CLLO list except "None".
            /*
            let selectedCourse = courseSelect.find("option:selected");
            if (! selectedCourse.length) {
                $(QSC_CMP_FORM_CLLO_PARENT_SELECT).find("option").remove();
            }
            */
        }
    );    
}

function qscCMPCLLOHandleCourseSelectionForParentCLLO() {
    // Get the current CLLO ID
    let currentCLLOID = $(QSC_CMP_FORM_CLLO_ID).val();
    
    // Remove the current set of possible parent CLLOs
    let parentCLLOSelect = $(QSC_CMP_FORM_CLLO_PARENT_COURSE_CLLO_LIST);
    parentCLLOSelect.find("option").remove();

    // Get the currently selected parent course
    let selectedCourse = $(QSC_CMP_FORM_CLLO_PARENT_COURSE_LIST).find("option:selected");
    if (! selectedCourse.length) {
        return;
    }

    // Prep the AJAX data and get the associated CLLOs
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

function qscCMPCLLOHandlePLLOInput() {
    // AJAX isn't necessary here as as the PLLO options were loaded when the 
    // course was selected. All that's needed is a filter.
    let plloInputText = $(QSC_CMP_FORM_CLLO_PLLO_INPUT).val();
    let plloPossibleSelectOptions = $(QSC_CMP_FORM_CLLO_PLLO_LIST_POSSIBLE).find("option");

    // If there's no text then display all of options
    if (! plloInputText) {
        plloPossibleSelectOptions.show();
        return;
    }     

    // Go through each possible PLLO to select
    plloInputText = plloInputText.toLowerCase();
    plloPossibleSelectOptions.each(function() {
        let optionText = $(this).text();
        if (! optionText) {
            return;
        }
        
        optionText = optionText.toLowerCase();
        if (optionText.indexOf(plloInputText) === -1) {
            $(this).hide();
        }
        else {
            $(this).show();
        }
    });
}

function qscCMPCLLOHandleCourseChangeForPLLOs () {
    // Any change to the course selection means removing the prior PLLO options
    let plloPossibleSelect = $(QSC_CMP_FORM_CLLO_PLLO_LIST_POSSIBLE);
    let plloSupportedSelect = $(QSC_CMP_FORM_CLLO_PLLO_LIST_SUPPORTED);
    plloPossibleSelect.find("option").remove();
    plloSupportedSelect.find("option").remove();    

    // Get the set of currently selected courses
    let selectCoursesIDArray = qscCMPGetSelectedCoursesIDArray();
        
    // Prep the AJAX data and get the associated PLLOs
    ajaxData = {action: QSC_CMP_AJAX_ACTION_GET_PLLOS_FOR_SEVERAL_COURSES,
        id_array: selectCoursesIDArray,
        exclude: []
    };    
    qscCorePerformAJAXRequest(QSC_CMP_AJAX_SCRIPT_GET_PLLOS, ajaxData,
        function (jsonData) {
            // Create new options from the JSON data and put them in the select
            plloPossibleSelect.append(
                qscCoreCreateOptionsFromJSONData(jsonData, 'id', 'name'));
        }
    );  
    
}


/******************************************************************************
 * Document - Ready
 *****************************************************************************/
$(document).ready(function() {
    // Handle the user editing the selected course
    $(QSC_CMP_FORM_CLLO_COURSE_INPUT).keyup(qscCMPCLLOHandleCourseInput);
            
    // Handle the user selecting the '>>' and '<<' buttons to move courses
    // back and forth and handle the associated levels        
    $(QSC_CMP_FORM_CLLO_COURSE_AND_LEVEL_ADD).click(function() {
        let selectedCourseOption = $(QSC_CMP_FORM_CLLO_COURSE_LIST_POSSIBLE + ' option:selected');
        let selectedLevelOption = $(QSC_CMP_FORM_CLLO_LEVEL_LIST_POSSIBLE + ' option:selected');
        let clonedSelectedLevelOption = null;
        let clonedSelectedCourseOption = null;

        if (selectedCourseOption.length && selectedLevelOption.length) {
            selectedCourseOption.appendTo(QSC_CMP_FORM_CLLO_COURSE_LIST_SELECTED);
            
            // Add the course to the 'course and level' list
            clonedSelectedLevelOption = selectedLevelOption.clone();
            clonedSelectedLevelOption.appendTo(QSC_CMP_FORM_CLLO_LEVEL_LIST_SELECTED);
            clonedSelectedLevelOption.prop("selected", true);
            
            // Add the course to the 'Parent CLLO course' list
            clonedSelectedCourseOption = selectedCourseOption.clone();
            clonedSelectedCourseOption.appendTo(QSC_CMP_FORM_CLLO_PARENT_COURSE_LIST);            
        }
        
        qscCMPCLLOHandleCourseChangeForPLLOs();        
    });    
    $(QSC_CMP_FORM_CLLO_COURSE_AND_LEVEL_REMOVE).click(function() {        
        let selectedCourseIndex = $(QSC_CMP_FORM_CLLO_COURSE_LIST_SELECTED).prop('selectedIndex');

        qscCoreTransferOption(QSC_CMP_FORM_CLLO_COURSE_LIST_SELECTED, 
            QSC_CMP_FORM_CLLO_COURSE_LIST_POSSIBLE);

        if (selectedCourseIndex !== -1) {
            // Check whether the removed course was currently selected for 
            // the parent CLLO
            let clloParentSelectedCourseIndex = $(QSC_CMP_FORM_CLLO_PARENT_COURSE_LIST).prop('selectedIndex');
            if (clloParentSelectedCourseIndex === selectedCourseIndex) {
                $(QSC_CMP_FORM_CLLO_PARENT_COURSE_CLLO_LIST).find("option").remove();
            }
            
            $(QSC_CMP_FORM_CLLO_PARENT_COURSE_LIST).find("option").eq(selectedCourseIndex).remove();
        }
                
        $(QSC_CMP_FORM_CLLO_LEVEL_LIST_SELECTED + ' option:selected').remove();
        
        qscCMPCLLOHandleCourseChangeForPLLOs();        
    });        
    
    // Coordinate the selection of a course/level with the corresponding level/course
    $(QSC_CMP_FORM_CLLO_COURSE_LIST_SELECTED).click(function(event) {
        qscCoreCoordinateSelectedItem(event, 
            QSC_CMP_FORM_CLLO_COURSE_LIST_SELECTED, 
            QSC_CMP_FORM_CLLO_LEVEL_LIST_SELECTED);
    });
    $(QSC_CMP_FORM_CLLO_LEVEL_LIST_SELECTED).click(function(event) {
        qscCoreCoordinateSelectedItem(event, 
            QSC_CMP_FORM_CLLO_LEVEL_LIST_SELECTED, 
            QSC_CMP_FORM_CLLO_COURSE_LIST_SELECTED);
    });    
    
    // Handle the user changing the selected course(s) for the parent CLLO, which 
    // must change the possible parent CLLO options
    $(QSC_CMP_FORM_CLLO_PARENT_COURSE_LIST).change(qscCMPCLLOHandleCourseSelectionForParentCLLO);
    
    // Handle the user 'unselecting' the parent CLLO because Ctrl + click
    // doesn't unselect an option in a single select box
    $(QSC_CMP_FORM_CLLO_PARENT_COURSE_CLLO_UNSELECT).click(function() {
        $(QSC_CMP_FORM_CLLO_PARENT_COURSE_CLLO_LIST + ' option:selected').prop("selected", false);    
    });
                
    // Handle the user entering text to filter the PLLO options
    $(QSC_CMP_FORM_CLLO_PLLO_INPUT).keyup(qscCMPCLLOHandlePLLOInput);
    
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
    
    // Handle form submission for add and edit
    $(QSC_CMP_FORM_CLLO_ADD + ', ' + QSC_CMP_FORM_CLLO_EDIT).submit(function(event) {
        // Make sure there's at least one course-level option selected.
        if ($(QSC_CMP_FORM_CLLO_COURSE_LIST_SELECTED + " option").length == 0) {
            event.preventDefault();
        }
        
        // Select all of the options in the selected Course and Level lists
        // so they appear in $_POST
        qscCoreSelectAllOptions(QSC_CMP_FORM_CLLO_COURSE_LIST_SELECTED);
        qscCoreSelectAllOptions(QSC_CMP_FORM_CLLO_LEVEL_LIST_SELECTED);
        // 
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