/**
 * JavaScript function definitions for client-side bundle features.
 * These all require jQuery to work.
 *
 * @uses Translator from willdurand/js-translation-bundle (if available)
 *
 * If using the above translation bundle, note that the client-side translation
 * strings for willherzog/symfony-form-bundle are in the "scripts" domain.
 */

/**
 * jQuery needs to be available, either as the global variable "$" or the global variable "jQuery".
 * If only the latter exists, a new local variable is created with the same name as the former.
 */
if( typeof $ !== 'function' && typeof jQuery !== 'function' ) {
	throw new ReferenceError('The jQuery framework does not appear to be available.');
} else if( typeof $ === 'undefined' && typeof jQuery === 'function' ) {
	var $ = jQuery;
}

const haveTranslator = typeof Translator === 'object' && typeof Translator.trans === 'function';
const disabledWidgetTooltip = haveTranslator ? Translator.trans('wh_form.term.disabled') : 'Disabled'; // used by makeStylableCheckboxWidget() & makeStylableRadioWidget()
const collapsibleFieldsetTooltipExpand = haveTranslator ? Translator.trans('wh_form.action.expand') : 'Expand'; // used by enableCollapsibleFieldsets()
const collapsibleFieldsetTooltipCollapse = haveTranslator ? Translator.trans('wh_form.action.collapse') : 'Collapse'; // used by enableCollapsibleFieldsets()
const defaultRemoveButtonTooltip = haveTranslator ? Translator.trans('wh_form.action.remove') : 'Remove'; // used by createRemoveButtonElement()

/**
 * Allow a "toggle switch" widget to be toggled on and off.
 *
 * @author Will Herzog <willherzog@gmail.com>
 *
 * @param {JQuery} container jQuery instance containing the expected HTML output for a "toggle switch" widget
 */
function activateToggleSwitch(container) {
	if( typeof container !== 'object' || !(container instanceof $) ) {
		throw new TypeError('The "container" argument must be a jQuery object.');
	}

	const toggle = container.children('button.toggle-switch-widget');
	const checkbox = container.children('input[type="checkbox"]');
	const label = container.children('label').first();

	function updateCheckboxState(e) {
		e.preventDefault();

		toggle.toggleClass('enabled');

		if( toggle.hasClass('enabled') ) {
			checkbox.prop('checked', true);
		} else {
			checkbox.prop('checked', false);
		}

		checkbox.trigger('change');
	}

	toggle.on('click', updateCheckboxState);
	label.on('click', updateCheckboxState);
}

/**
 * Add an easily-styled element (with the class "checkbox-widget") as a sibling of a checkbox widget and keep the state of both elements in sync.
 * The real checkbox widget can be hidden via CSS by selecting elements with the "hidden-widget" class.
 *
 * @author Will Herzog <willherzog@gmail.com>
 *
 * @param {JQuery} realWidget jQuery instance of a single HTML input tag with type="checkbox"
 * @param {boolean} [addTooltipIfDisabled] Whether to add the title attribute "Disabled" if the widget is disabled
 */
function makeStylableCheckboxWidget(realWidget, addTooltipIfDisabled = true) {
	if( typeof realWidget !== 'object' || !(realWidget instanceof $) ) {
		throw new TypeError('The "realWidget" argument must be a jQuery object.');
	}

	// prevent affecting checkbox widgets for toggle switches (or those with which this function has already been used)
	if( realWidget.hasClass('hidden-widget') ) {
		return;
	}

	const fakeWidget = $('<div class="stylable-widget checkbox-widget" tabindex="0"></div>');

	realWidget.addClass('hidden-widget').attr('tabindex', -1).after(fakeWidget);

	if( realWidget.is(':checked') ) {
		fakeWidget.addClass('checked');
	}

	if( realWidget.is(':disabled') ) {
		fakeWidget.addClass('disabled');

		if( addTooltipIfDisabled ) {
			fakeWidget.attr('title', disabledWidgetTooltip);
		}
	}

	if( !realWidget.val() ) {
		realWidget.val(1);
	}

	realWidget.siblings(`label[for="${realWidget.attr('id')}"]`).add(fakeWidget).on('click', e => {
		e.preventDefault();

		if( realWidget.is(':disabled') ) {
			return;
		}

		if( fakeWidget.hasClass('checked') ) {
			fakeWidget.removeClass('checked');
			realWidget.prop('checked', false);
		} else {
			fakeWidget.addClass('checked');
			realWidget.prop('checked', true);
		}

		realWidget.trigger('change');
	});
}

/**
 * Add an easily-styled element (with the class "radio-widget") as a sibling of a radio widget and keep the state of both elements in sync.
 * The real radio widget can be hidden via CSS by selecting elements with the "hidden-widget" class.
 *
 * @author Will Herzog <willherzog@gmail.com>
 *
 * @param {JQuery} realWidget jQuery instance of a single HTML input tag with type="radio"
 * @param {boolean} [addTooltipIfDisabled] Whether to add the title attribute "Disabled" if the widget is disabled
 */
function makeStylableRadioWidget(realWidget, addTooltipIfDisabled = true) {
	if( typeof realWidget !== 'object' || !(realWidget instanceof $) ) {
		throw new TypeError('The "realWidget" argument must be a jQuery object.');
	}

	// prevent affecting radio widgets with which this function has already been used
	if( realWidget.hasClass('hidden-widget') ) {
		return;
	}

	const fakeWidget = $('<div class="stylable-widget radio-widget" tabindex="0"></div>');

	realWidget.addClass('hidden-widget').attr('tabindex', -1).after(fakeWidget);

	if( realWidget.is(':checked') ) {
		fakeWidget.addClass('checked');
	}

	if( realWidget.is(':disabled') ) {
		fakeWidget.addClass('disabled');

		if( addTooltipIfDisabled ) {
			fakeWidget.attr('title', disabledWidgetTooltip);
		}
	}

	if( !realWidget.val() ) {
		realWidget.val(1);
	}

	realWidget.siblings(`label[for="${realWidget.attr('id')}"]`).add(fakeWidget).on('click', e => {
		e.preventDefault();

		if( realWidget.is(':disabled') ) {
			return;
		}

		if( !fakeWidget.hasClass('checked') ) {
			let groupedWidgets = fakeWidget.parents('.form-field.radio').find(`input[type=radio][name="${realWidget.attr('name')}"]`),
				selectedWidget = groupedWidgets.filter(':checked');

			selectedWidget.prop('checked', false);
			selectedWidget.siblings('.radio-widget').removeClass('checked');

			fakeWidget.addClass('checked');
			realWidget.prop('checked', true);

			realWidget.trigger('change');
		}
	});
}

/**
 * Allow fieldsets with the class "collapsible" to be collapsed and expanded.
 *
 * @author Will Herzog <willherzog@gmail.com>
 *
 * @param {JQuery} [container] Optional jQuery instance containing any fieldsets to be made collapsible (defaults to all form elements)
 */
function enableCollapsibleFieldsets(container = null) {
	if( container === null ) {
		container = $('form');
	} else if( typeof container !== 'object' || !(container instanceof $) ) {
		throw new TypeError('The "container" argument must be a jQuery object or NULL.');
	}

	const fieldsets = container.find('fieldset.collapsible');

	if( fieldsets.length > 0 ) {
		fieldsets.children(':not(legend)').hide();
		fieldsets
			.prepend(`<div class="expander expand" title="${collapsibleFieldsetTooltipExpand}"></div>`)
			.on('click', '.expander', function () {
				const expander = $(this);

				expander.parent('fieldset').children(':not(legend)').not(expander).slideToggle(400);

				window.setTimeout(() => {
					expander.toggleClass('expand collapse');

					if( expander.hasClass('collapse') ) {
						expander.attr('title', collapsibleFieldsetTooltipCollapse);
					} else {
						expander.attr('title', collapsibleFieldsetTooltipExpand);
					}
				}, 400);
			});
	}
}

/**
 * Allow faux number widget wrapper elements to be styled the same as normal focused widgets (via the class "widget-has-focus") when the inner widget receives focus.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
function setupFauxNumberWrapperWidgets() {
	$('.faux-number-widget.with-type-symbol').each(function () {
		const fauxWrapperWidget = $(this);

		fauxWrapperWidget.on('focus', ':input', () => {
			fauxWrapperWidget.addClass('widget-has-focus');
		});

		fauxWrapperWidget.on('blur', ':input', () => {
			fauxWrapperWidget.removeClass('widget-has-focus');
		});
	});
}

const textBasedInputsSelector = 'input[type="text"], input[type="email"], input[type="url"], input[type="tel"], input[type="number"], input[type="search"], input[type="password"], input[type="month"], input[type="week"], textarea';

/**
 * Given a text-based input element with a non-empty value, move the cursor to after the last character.
 *
 * @author Will Herzog <willherzog@gmail.com>
 *
 * @param {JQuery} textBasedInput jQuery instance of a single HTML input (of a text-based type) or textarea element
 */
function moveCursorAfterLastCharacter(textBasedInput) {
	if( typeof textBasedInput !== 'object' || !(textBasedInput instanceof $) ) {
		throw new TypeError('The "textBasedInput" argument must be a jQuery object.');
	}

	if( textBasedInput.filter(textBasedInputsSelector).length === 0 || typeof textBasedInput.val === 'undefined' ) {
		return;
	}

	const widgetInputLength = textBasedInput.val().length;

	try {
		textBasedInput[0].setSelectionRange(widgetInputLength, widgetInputLength);
	} catch(e) {
		console.log(e);
	}
}

/**
 * Apply all preceding setup functions to a form (or all forms currently on the page).
 *
 * @author Will Herzog <willherzog@gmail.com>
 *
 * @param {JQuery} [container] Optional jQuery instance of a containing form element (defaults to all form elements)
 * @param {boolean} [focusFirst] Whether to switch focus to the first text-based input (in HTML markup order); if one such input is using the "autofocus" attribute, this will only apply moveCursorAfterLastCharacter() to it
 */
function setupFormFields(container = null, focusFirst = false) {
	if( container === null ) {
		container = $('form');
	} else if( typeof container !== 'object' || !(container instanceof $) ) {
		throw new TypeError('The "container" argument must be a jQuery object or NULL.');
	}

	// toggle switches (checkbox visual mod)
	container.find('.form-field.toggle-switch').each(function () {
		activateToggleSwitch($(this));
	});

	// stylable checkbox widgets
	container.find('input[type=checkbox]').each(function () {
		makeStylableCheckboxWidget($(this));
	});

	// stylable radio widgets
	container.find('input[type=radio]').each(function () {
		makeStylableRadioWidget($(this));
	});

	enableCollapsibleFieldsets(container);

	setupFauxNumberWrapperWidgets();

	if( focusFirst ) {
		const allTextBasedInputs = container.find(textBasedInputsSelector);
		let firstInput = allTextBasedInputs.filter('[autofocus]').first();

		if( firstInput.length === 0 ) {
			firstInput = allTextBasedInputs.not('[readonly]').filter(':enabled').first();

			firstInput.trigger('focus');
		}

		moveCursorAfterLastCharacter(firstInput);
	}
}

/**
 * Allow fieldsets with the class "collapsible" to be collapsed and expanded.
 *
 * @author Will Herzog <willherzog@gmail.com>
 *
 * @param {JQuery} selectWidget jQuery instance of a single HTML select element
 * @param {Array} valuesInUse An array of values for which the associated options should be disabled
 */
function changeSelectOptionsEnablement(selectWidget, valuesInUse) {
	if( typeof selectWidget !== 'object' || !(selectWidget instanceof $) ) {
		throw new TypeError('The "selectWidget" argument must be a jQuery object.');
	}

	if( !Array.isArray(valuesInUse) ) {
		throw new TypeError('The "valuesInUse" argument must be an array.');
	}

	selectWidget.find('option').each(function () {
		const option = $(this), value = option.attr('value');

		if( value && !option.is(':selected') ) {
			if( valuesInUse.includes(value) ) {
				option.prop('disabled', true);
			} else {
				option.prop('disabled', false);
			}
		}
	});
}

/**
 * Allow fieldsets with the class "collapsible" to be collapsed and expanded.
 *
 * @author Will Herzog <willherzog@gmail.com>
 *
 * @param {string} [tooltip] Optional string value to use for the remove button's title attribute (defaults to "Remove"; use an empty string or FALSE for no tooltip)
 */
function createRemoveButtonElement(tooltip) {
	if( tooltip === false ) {
		tooltip = '';
	} else if( tooltip !== '' ) {
		if( typeof tooltip !== 'string' ) {
			tooltip = defaultRemoveButtonTooltip;
		}

		tooltip = ` title="${tooltip}"`;
	}

	return $(`<button type="button" class="action remove"${tooltip}></button>`);
}

/**
 * Allow fieldsets with the class "collapsible" to be collapsed and expanded.
 *
 * @author Will Herzog <willherzog@gmail.com>
 *
 * @param {JQuery} subForm jQuery instance of a sub-form to which the remove action should be added
 * @param {string} [removalTooltip] [Optional] - see "tooltip" parameter of createRemoveButtonElement()
 * @param {Function} [callbackFn] Optional callback function to be called when the remove button is clicked
 * @param {*} [callbackData] Optional data to be used as the sole parameter for the callback function (if not set, the value of the "subform" parameter is used)
 */
function setupSubFormRemoveAction(subForm, removalTooltip, callbackFn, callbackData) {
	if( typeof subForm !== 'object' || !(subForm instanceof $) ) {
		throw new TypeError('The "subForm" argument must be a jQuery object.');
	}

	const removeButton = createRemoveButtonElement(removalTooltip);

	subForm.append(removeButton);

	removeButton.on('click', () => {
		if( typeof callbackFn === 'function' ) {
			if( typeof callbackData !== 'undefined' ) {
				callbackFn(callbackData);
			} else {
				callbackFn(subForm);
			}
		}

		subForm.remove();
	});
}

export {
	activateToggleSwitch,
	makeStylableCheckboxWidget,
	makeStylableRadioWidget,
	enableCollapsibleFieldsets,
	setupFauxNumberWrapperWidgets,
	textBasedInputsSelector,
	moveCursorAfterLastCharacter,
	setupFormFields,
	changeSelectOptionsEnablement,
	createRemoveButtonElement,
	setupSubFormRemoveAction
};
