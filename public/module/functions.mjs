/**
 * Javascript function definitions for client-side bundle features.
 * These all require jQuery to work.
 *
 * @uses Translator from willdurand/js-translation-bundle (if available)
 *
 * If using the above translation bundle, note that the client-side translation
 * strings for willherzog/symfony-form-bundle are in the "scripts" domain.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */

const haveTranslator = typeof Translator === 'object' && typeof Translator.trans === 'function';

const collapsibleFieldsetTooltipExpand = haveTranslator ? Translator.trans('wh_form.action.expand') : 'Expand';
const collapsibleFieldsetTooltipCollapse = haveTranslator ? Translator.trans('wh_form.action.collapse') : 'Collapse';
const defaultRemoveButtonTooltip = haveTranslator ? Translator.trans('wh_form.action.remove') : 'Remove';

function activateToggleSwitch(container) {
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

function enableCollapsibleFieldsets(container = null) {
	if( container === null ) {
		container = $('form');
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

function setupSubFormRemoveAction(subForm, removalTooltip, callbackFn, callbackData) {
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
	enableCollapsibleFieldsets,
	createRemoveButtonElement,
	setupSubFormRemoveAction
};
