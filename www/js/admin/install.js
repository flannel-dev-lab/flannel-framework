$(document).ready(function () {

    $("#enableSentry").change(function (e) {
        let enableSentry = $(this).val();
        if (enableSentry === 'true') {
            $("#sentryUrlBlock").prop("required", true);
            $("#sentryEnvironmentBlock").prop("required", true);
            $("#sentryUrlBlock").removeClass("d-none");
            $("#sentryEnvironmentBlock").removeClass("d-none");
        } else {
            $("#sentryUrlBlock").prop("required", false);
            $("#sentryEnvironmentBlock").prop("required", false);
            $("#sentryUrlBlock").addClass("d-none");
            $("#sentryEnvironmentBlock").addClass("d-none");
        }
    });

    // Toolbar extra buttons
    var btnFinish = $('<button type="submit"></button>').text('Finish')
        .addClass('btn sw-btn-finish d-none')
        .on('click', function () {
            if (!$('#installForm').valid()) {
                return;
            }
        });

    // SmartWizard initialize
    $('#smartwizard').smartWizard({
        selected: 0, // Initial selected step, 0 = first step
        theme: 'default', // theme for the wizard, related css need to include for other than default theme
        justified: true, // Nav menu justification. true/false
        darkMode: false, // Enable/disable Dark Mode if the theme supports. true/false
        autoAdjustHeight: true, // Automatically adjust content height
        backButtonSupport: true, // Enable the back button support
        enableURLhash: true, // Enable selection of the step based on url hash
        transition: {
            animation: 'none', // Effect on navigation, none/fade/slide-horizontal/slide-vertical/slide-swing
            speed: '400', // Transion animation speed
            easing: '' // Transition animation easing. Not supported without a jQuery easing plugin
        },
        toolbarSettings: {
            toolbarPosition: 'bottom', // none, top, bottom, both
            toolbarButtonPosition: 'right', // left, right, center
            showNextButton: true, // show/hide a Next button
            showPreviousButton: true, // show/hide a Previous button
            toolbarExtraButtons: [btnFinish] // Extra buttons to show on toolbar, array of jQuery input/buttons elements
        },
        anchorSettings: {
            anchorClickable: true, // Enable/Disable anchor navigation
            enableAllAnchors: false, // Activates all anchors clickable all times
            markDoneStep: true, // Add done state on navigation
            markAllPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
            removeDoneStepOnNavigateBack: false, // While navigate back done step after active step will be cleared
            enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
        },
        keyboardSettings: {
            keyNavigation: true, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
            keyLeft: [37], // Left key code
            keyRight: [39] // Right key code
        },
        lang: { // Language variables for button
            next: 'Next',
            previous: 'Previous'
        },
        disabledSteps: [], // Array Steps disabled
        errorSteps: [], // Highlight step with errors
        hiddenSteps: [] // Hidden steps
    });

    // Step show event
    $("#smartwizard").on("showStep", function (e, anchorObject, stepNumber, stepDirection, stepPosition) {
        if (stepPosition === 'last') {
            $(".sw-btn-next").addClass('d-none');
            $(".sw-btn-finish").removeClass('d-none');
        } else {
            $(".sw-btn-next").removeClass('d-none');
            $(".sw-btn-finish").addClass('d-none');
        }
    });

    $('#smartwizard').on("leaveStep", function (e, anchorObject, currentStepIndex, nextStepIndex, stepDirection) {
        var elmForm = $("#step-" + currentStepIndex);
        if (stepDirection === 'forward' && elmForm) {
            if ($('#installForm').valid()) {
                return true
            } else {
                return false
            }
        }
        return true;
    });

    $('#smartwizard').on("finish", function (e, anchorObject, currentStepIndex, nextStepIndex, stepDirection) {
        var elmForm = $("#step-" + currentStepIndex);
        if (stepDirection === 'forward' && elmForm) {
            if ($('#installForm').valid()) {
                return true
            } else {
                return false
            }
        }
        return true;
    });

});
