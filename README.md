# WHFormBundle
An assortment of mostly relatively small changes and extensions to the Symfony Form component.
This bundle's focus is primarily on form presentation (as opposed to form processing).

## Form Type Extensions

* `BaseTypeExtension` – Adds the `translate_attributes`, `help_markdown_lines`, `use_parent_row_type` and `immutable` options to all field types; it also automatically adds HTML classes based on the field type, field name and whether the field is required or disabled to the field row (these HTML classes, as well as the field's `id` attribute, have underscores converted to dashes*)
* `FormTypesExtension` – Makes most core Symfony types default to not being required* (field groups—`CollectionType` in particular—are not included)
* `ChoiceTypeExtension` – Adds the `expanded_wrapping_strategy` and `placeholder_translation_parameters` options to `ChoiceType` fields
* `CollectionTypeExtension`  – Adds the `add_button_label`, `add_button_position`, `add_button_class` and `help_text_position` options to `CollectionType` field groups
* `EnumTypeExtension` – Leverages the included `LabelAwareEnum` and `LimitedChoicesEnum` interfaces to add automation by default to the `choice_label` and `choice_filter` options for EnumType fields
* `CheckboxTypeExtension` – Adds the `label_after_widget` option to `CheckboxType` fields
* `ButtonTypeExtension` – Adds the `help`, `help_attr`, `help_html` and `help_translation_parameters` options to `ButtonType` fields
* `NumberTypesExtension` – Automatically adds the HTML class "faux-number-widget" when the `html5` option is FALSE for `NumberType`, `MoneyType` and `PercentType` fields, allowing these to more easily have unified styling applied that distinguishes their widgets from other text inputs
* `MoneyTypeExtension` – Alters the original approach of Symfony's `MoneyType` fields to allow more flexibility with the money symbol

## Custom Form Types

* `FieldsetType` – Allows using `<fieldset>` elements to group related fields together; these can include expanding/collapsing functionality with use of a JavaScript function
* `ToggleSwitchType` & `ToggleSwitchWithSettingsType` – Visual variation of `CheckboxType` which uses a `<button>` element to control interaction (these depend on a JavaScript function for activation); the latter type also includes a "Settings" button intended to open a modal dialog
* `InfoType` – Provides a way to display a current value within a form without allowing it to be processed upon form submission
* `IncludeType` – Allows inclusion of a Twig template fragment within a form
* `DividerType` – Allows inclusion of a horizontal rule (i.e. an `<hr>` tag) within a form
* `ActionType` – A custom alternative to Symfony's `ButtonType` which has a separate `button_label` option (in addition to the standard field `label`); it also always has the HTML class "action"
* `ModalEditorType` – Similar to `ActionType`, but is mapped to the underlying data (using a hidden input) and always includes the HTML attribute `aria-haspopup="dialog"`
* `ModalSelectType`, `ModalImageSelectType` and `ModalEnumSelectType` – Button-based types specifically for selecting a value from a modal dialog (the value is stored in a hidden input); these also always includes the HTML attribute `aria-haspopup="dialog"`

## Other Features

There are also several changes directly in the form layout template (based on the `form_div_layout.html.twig` template from `symfony/twig-bridge`):

* All regular field rows automatically have the HTML class "form-field", all `CollectionType` rows have the HTML class "form-group" and rows for Symfony's button types have the HTML class "form-action"
* The "widget" (i.e. container element) for `CollectionType` field groups has the HTML classes "sub-form-container" and "group-members" automatically added; entries with multiple fields receive the HTML classes "sub-form" and "group-member"
* Widget blocks for `MoneyType` and `PercentType` fields—whenever they have a symbol set—include an "input-type-symbol" (using `<span>`), which is wrapped—together with the actual input element—in a `<div>` with the HTML classes "faux-number-widget" and "with-type-symbol" (the latter class is also added to their rows for maximum versatility with styling the symbol elements)
* Form/field errors are output as an unordered list—the list has the HTML class "error-list" and the items have the class "error"
* Automatically-adjusting indentation levels are applied to the HTML output using the custom Twig functions `form_indent()`, `form_current_indent_level()`, `form_increment_indent_level()`, `form_decrement_indent_level()` and `form_set_indent_level()` (note: the latter three should only be used w/the Twig `{% do %}` tag as they don't return anything)

Lastly (although this list of the bundle's features is non-exhaustive), this bundle includes the following static/client-side files, which should be available under your project's `public/bundles/whform/` directory:

* `module/functions.mjs` – Exports the `activateToggleSwitch`, `enableCollapsibleFieldsets`, `createRemoveButtonElement` and `setupSubFormRemoveAction` functions (the latter two are intended for use with entries of `CollectionType` field groups)
* `style/form.css` – Functional-only style rules for this bundle's features

\* These features are configurable.


## Bundle Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require willherzog/symfony-form-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require willherzog/symfony-form-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    WHSymfony\WHFormBundle\WHFormBundle::class => ['all' => true],
];
```

## Default Configuration

```yaml
# config/packages/wh_form.yaml

wh_form:
    indent_spaces: 0 # Whether to use spaces (instead of tab), as well as how many, for each indentation level
    form:
        default_indent: 2 # Starting indentation level when not using form_set_indent_level(x) (or after calling it without an argument)
        default_optional: true # Whether to make most fields optional by default
        id_attributes_use_dashes: true # Whether to convert underscores in field names to dashes (for use with the HTML "id" attribute on the field widget)
```
