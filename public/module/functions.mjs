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
 * When only one of these is available, a new local variable is created with the same name as the other one.
 */
if( typeof $ === 'undefined' && typeof jQuery === 'function' ) {
	var $ = jQuery;
} else if( typeof jQuery === 'undefined' && typeof $ === 'function' ) {
	var jQuery = $;
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
 * @param container jQuery instance containing the expected HTML output for a "toggle switch" widget
 */
function activateToggleSwitch(container) {
	if( typeof container !== 'object' || !(container instanceof jQuery) ) {
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
 * @param realWidget jQuery instance of a single HTML input tag with type="checkbox"
 * @param addTooltipIfDisabled Whether to add the title attribute "Disabled" if the widget is disabled
 */
function makeStylableCheckboxWidget(realWidget, addTooltipIfDisabled = true) {
	if( typeof realWidget !== 'object' || !(realWidget instanceof jQuery) ) {
		throw new TypeError('The "realWidget" argument must be a jQuery object.');
	}

	// prevent affecting checkbox widgets for toggle switches (or those with which this function has already been used)
	if( realWidget.hasClass('hidden-widget') ) {
		return;
	}

	const fakeWidget = $('<div class="checkbox-widget" tabindex="0"></div>');

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
 * @param realWidget jQuery instance of a single HTML input tag with type="radio"
 * @param addTooltipIfDisabled Whether to add the title attribute "Disabled" if the widget is disabled
 */
function makeStylableRadioWidget(realWidget, addTooltipIfDisabled = true) {
	if( typeof realWidget !== 'object' || !(realWidget instanceof jQuery) ) {
		throw new TypeError('The "realWidget" argument must be a jQuery object.');
	}

	// prevent affecting radio widgets with which this function has already been used
	if( realWidget.hasClass('hidden-widget') ) {
		return;
	}

	const fakeWidget = $('<div class="radio-widget" tabindex="0"></div>');

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
 * @param container Optional jQuery instance containing any fieldsets to be made collapsible (defaults to all form elements)
 */
function enableCollapsibleFieldsets(container = null) {
	if( container === null ) {
		container = $('form');
	} else if( typeof container !== 'object' || !(container instanceof jQuery) ) {
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
 * Allow fieldsets with the class "collapsible" to be collapsed and expanded.
 *
 * @author Will Herzog <willherzog@gmail.com>
 *
 * @param tooltip Optional string value to use for the remove button's title attribute (defaults to "Remove"; use an empty string or FALSE for no tooltip)
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
 * @param subForm jQuery instance of a sub-form to which the remove action should be added
 * @param removalTooltip Optional - see "tooltip" parameter of createRemoveButtonElement()
 * @param callbackFn Optional callback function to be called when the remove button is clicked
 * @param callbackData Optional data to be used as the sole parameter for the callback function (if not set, the value of the "subform" parameter is used)
 */
function setupSubFormRemoveAction(subForm, removalTooltip, callbackFn, callbackData) {
	if( typeof subForm !== 'object' || !(subForm instanceof jQuery) ) {
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
	createRemoveButtonElement,
	setupSubFormRemoveAction
};
