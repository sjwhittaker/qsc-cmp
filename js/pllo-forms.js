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
const QSC_CMP_FORM_PLLO_ADD = "#form-pllo-add";
const QSC_CMP_FORM_PLLO_EDIT = "#form-pllo-edit";
const QSC_CMP_FORM_PLLO_DELETE = "#form-pllo-delete";

const QSC_CMP_FORM_PLLO_ID = "#pllo-id";
const QSC_CMP_FORM_PLLO_NUMBER = "#pllo-number";

const QSC_CMP_FORM_PLLO_PLAN_INPUT = "#pllo-plan-input";
const QSC_CMP_FORM_PLLO_PLAN_LIST_POSSIBLE = "#pllo-plan-list-possible";
const QSC_CMP_FORM_PLLO_PLAN_LIST_SUPPORTED = "#pllo-plan-list-supported";
const QSC_CMP_FORM_PLLO_PLAN_ADD = "#pllo-plan-add";
const QSC_CMP_FORM_PLLO_PLAN_REMOVE = "#pllo-plan-remove";

const QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT = "#pllo-parent-dle-select";
const QSC_CMP_FORM_PLLO_PARENT_DLE_UNSELECT = "#pllo-parent-dle-unselect";

const QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT = "#pllo-parent-pllo-select";
const QSC_CMP_FORM_PLLO_PARENT_PLLO_UNSELECT = "#pllo-parent-pllo-unselect";

const QSC_CMP_FORM_PLLO_TEXT = "#pllo-text";

const QSC_CMP_FORM_PLLO_ILO_LIST_POSSIBLE = "#pllo-ilo-list-possible";
const QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED = "#pllo-ilo-list-supported";
const QSC_CMP_FORM_PLLO_ILO_ADD = "#pllo-ilo-add";
const QSC_CMP_FORM_PLLO_ILO_REMOVE = "#pllo-ilo-remove"; 

const QSC_CMP_FORM_PLLO_NOTES = "#pllo-notes";


/******************************************************************************
 * Functions
 *****************************************************************************/
function qscCMPPLLOHandlePlanInput() {
    // Get the current value that the user's entered in the input box
    let currentPlanValue = $(this).val();

    // Prep the AJAX data
    let ajaxData = {action: QSC_CMP_AJAX_ACTION_SEARCH_PLANS_FOR_PLLOS,
        search: currentPlanValue
    };
    
    // Get the select boxes for the 'possible' and supported lists
    let possiblePlansSelect = $(QSC_CMP_FORM_PLLO_PLAN_LIST_POSSIBLE);
    let supportedPlansSelect = $(QSC_CMP_FORM_PLLO_PLAN_LIST_SUPPORTED);

    // Remove all of the current possible options
    possiblePlansSelect.find("option").remove();

    qscCorePerformAJAXRequest(QSC_CMP_AJAX_SCRIPT_GET_PLANS, ajaxData,
        function (jsonData) {
            // Create new options from the JSON data and put them in the select
            possiblePlansSelect.append(qscCoreCreateOptionsFromJSONData(jsonData, 'id', 'name'));

            // Go through the supported options and remove those in the
            // possible list            
            supportedPlansSelect.find("option").each(function() {
                let supportedID = $(this).val();
                possiblePlansSelect.find('option[value="' + supportedID + '"').remove();
            });            
        }
    );    
}

function qscCMPPLLOHandlePlanAdditionAndRemoval() {
    // NOTE: this function is called *after* the option has been moved; all
    // that remains is to update the PLLOs

    // Get the ID of the current PLLO
    let currentPLLOID = $(QSC_CMP_FORM_PLLO_ID).val();
    
    // Get the currently selected plans in the 'supported' box
    let supportedPlansSelect = $(QSC_CMP_FORM_PLLO_PLAN_LIST_SUPPORTED);
    let supportedPlanArray = supportedPlansSelect.find("option");
    let supportedPlanIDArray = [];
    
    // Get the IDs for the plans
    supportedPlanArray.each(function() {
        supportedPlanIDArray.push($(this).val());
    });

    // Remove the existing parent PLLO options
    let parentPLLOSelect = $(QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT);
    parentPLLOSelect.find("option").remove();

    // Prep the AJAX data
    let ajaxData = {action: QSC_CMP_AJAX_ACTION_GET_PLLOS_FOR_PLANS,
        id: supportedPlanIDArray,
        exclude: [currentPLLOID]
    };
        
    // Put the PLLOs in the list of options
    qscCorePerformAJAXRequest(QSC_CMP_AJAX_SCRIPT_GET_PLLOS, ajaxData,
        function (jsonData) {
            // Create new options from the JSON data and put them in the select
            parentPLLOSelect.append(
                qscCoreCreateOptionsFromJSONData(jsonData, 'id', 'name'));

            // Remove the current PLLO ID, if it was a match
            // Now done in query
            //parentPLLOSelect.find('option[value="' + currentPLLOID + '"]').remove();
        }
    );
}


/******************************************************************************
 * Document - Ready
 *****************************************************************************/
$(document).ready(function() {
    // Handle the user searching for plans
    $(QSC_CMP_FORM_PLLO_PLAN_INPUT).keyup(qscCMPPLLOHandlePlanInput);
    
    // Handle the user selecting the '>>' and '<<' buttons to move plans
    // back and forth, i8ncluding altering the PLLOs
    qscCoreTransferOptionOnClick(QSC_CMP_FORM_PLLO_PLAN_ADD,
        QSC_CMP_FORM_PLLO_PLAN_LIST_POSSIBLE, 
        QSC_CMP_FORM_PLLO_PLAN_LIST_SUPPORTED);
    qscCoreTransferOptionOnClick(QSC_CMP_FORM_PLLO_PLAN_REMOVE,
        QSC_CMP_FORM_PLLO_PLAN_LIST_SUPPORTED, 
        QSC_CMP_FORM_PLLO_PLAN_LIST_POSSIBLE);
        
    $(QSC_CMP_FORM_PLLO_PLAN_ADD).click(qscCMPPLLOHandlePlanAdditionAndRemoval);
    $(QSC_CMP_FORM_PLLO_PLAN_REMOVE).click(qscCMPPLLOHandlePlanAdditionAndRemoval);
    
    // Handle the user 'unselecting' the DLE because Ctrl + click
    // doesn't unselect an option in a single select box
    qscCoreUnselectWithButton(QSC_CMP_FORM_PLLO_PARENT_DLE_UNSELECT, 
        QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT);
    qscCoreUnselectWithButton(QSC_CMP_FORM_PLLO_PARENT_PLLO_UNSELECT, 
        QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT);
            
    // If a parent PLLO is selected, any selected DLE should be unselected
    // and vice-versa.
    qscCoreUnselectOnChange(QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT,
        QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT);     
    qscCoreUnselectOnChange(QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT,
        QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT);
        
    // Handle the user selecting the '>>' and '<<' buttons to move ILOs 
    // back and forth
    qscCoreTransferOptionOnClick(QSC_CMP_FORM_PLLO_ILO_ADD,
        QSC_CMP_FORM_PLLO_ILO_LIST_POSSIBLE, 
        QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED);
    qscCoreTransferOptionOnClick(QSC_CMP_FORM_PLLO_ILO_REMOVE,
        QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED, 
        QSC_CMP_FORM_PLLO_ILO_LIST_POSSIBLE);        
    
    // Handle form submission for add and edit
    $(QSC_CMP_FORM_PLLO_ADD + ", " + QSC_CMP_FORM_PLLO_EDIT).submit(function(event) {            
        // At least one plan must be selected
        let plansSelected = $(QSC_CMP_FORM_PLLO_PLAN_LIST_POSSIBLE + ' option').length;        
        if (plansSelected === 0) {
            // To Do: figure out an accessible way to communicate the problem
            event.preventDefault();
            return;
        }
        
        // Either a DLE or parent PLLO must be selected
        let dleSelected = $(QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT + ' option:selected').length;
        let parentPLLOSelected = $(QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT + ' option:selected').length;
        
        if ((dleSelected + parentPLLOSelected) === 0) {
            // To Do: figure out an accessible way to communicate the problem
            event.preventDefault();
            return;
        }
        
        // Select all of the options in the supported plan and ILO lists so they 
        // appear in $_POST
        qscCoreSelectAllOptions(QSC_CMP_FORM_PLLO_PLAN_LIST_SUPPORTED);
        qscCoreSelectAllOptions(QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED);
        
        // Set the flag that JS is working
        qscCMPSetJSCompletedOnSubmission(); 
    });
    
    // Handle form submission for delete
    $(QSC_CMP_FORM_PLLO_DELETE).submit(function() {
        return qscCoreConfirmDelete("PLLO");
    });     
    
});