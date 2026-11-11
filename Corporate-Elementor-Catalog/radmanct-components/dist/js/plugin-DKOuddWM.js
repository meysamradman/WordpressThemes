//#region node_modules/tom-select/dist/esm/plugins/dropdown_input/plugin.js
/**
* Tom Select v2.6.2
* Licensed under the Apache License, Version 2.0 (the "License");
*/
var KEY_ESC = 27;
var KEY_TAB = 9;
/**
* Converts a scalar to its best string representation
* for hash keys and HTML attribute values.
*
* Transformations:
*   'str'     -> 'str'
*   null      -> ''
*   undefined -> ''
*   true      -> '1'
*   false     -> '0'
*   0         -> '0'
*   1         -> '1'
*
*/
/**
* Prevent default
*
*/
var preventDefault = (evt, stop = false) => {
	if (evt) {
		evt.preventDefault();
		if (stop) evt.stopPropagation();
	}
};
/**
* Add event helper
*
*/
var addEvent = (target, type, callback, options) => {
	target.addEventListener(type, callback, options);
};
/**
* Iterates over arrays and hashes.
*
* ```
* iterate(this.items, function(item, id) {
*    // invoked for each item
* });
* ```
*
*/
var iterate = (object, callback) => {
	if (Array.isArray(object)) object.forEach(callback);
	else for (var key in object) if (object.hasOwnProperty(key)) callback(object[key], key);
};
/**
* Return a dom element from either a dom query string, jQuery object, a dom element or html string
* https://stackoverflow.com/questions/494143/creating-a-new-dom-element-from-an-html-string-using-built-in-dom-methods-or-pro/35385518#35385518
*
* param query should be {}
*/
var getDom = (query) => {
	if (query.jquery) return query[0];
	if (query instanceof HTMLElement) return query;
	if (isHtmlString(query)) {
		var tpl = document.createElement("template");
		tpl.innerHTML = query.trim();
		return tpl.content.firstChild;
	}
	return document.querySelector(query);
};
var isHtmlString = (arg) => {
	if (typeof arg === "string" && arg.indexOf("<") > -1) return true;
	return false;
};
/**
* Add css classes
*
*/
var addClasses = (elmts, ...classes) => {
	var norm_classes = classesArray(classes);
	elmts = castAsArray(elmts);
	elmts.map((el) => {
		norm_classes.map((cls) => {
			el.classList.add(cls);
		});
	});
};
/**
* Return arguments
*
*/
var classesArray = (args) => {
	var classes = [];
	iterate(args, (_classes) => {
		if (typeof _classes === "string") _classes = _classes.trim().split(/[\t\n\f\r\s]/);
		if (Array.isArray(_classes)) classes = classes.concat(_classes);
	});
	return classes.filter(Boolean);
};
/**
* Create an array from arg if it's not already an array
*
*/
var castAsArray = (arg) => {
	if (!Array.isArray(arg)) arg = [arg];
	return arg;
};
/**
* Plugin: "dropdown_input" (Tom Select)
* Copyright (c) contributors
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not use this
* file except in compliance with the License. You may obtain a copy of the License at:
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software distributed under
* the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF
* ANY KIND, either express or implied. See the License for the specific language
* governing permissions and limitations under the License.
*
*/
function plugin() {
	const self = this;
	self.settings.shouldOpen = true;
	self.hook("before", "setup", () => {
		var _self$input;
		self.focus_node = self.control;
		addClasses(self.control_input, "dropdown-input");
		const div = getDom("<div class=\"dropdown-input-wrap\">");
		div.append(self.control_input);
		self.dropdown.insertBefore(div, self.dropdown.firstChild);
		const placeholder = getDom("<input class=\"items-placeholder\" tabindex=\"-1\" />");
		placeholder.placeholder = self.settings.placeholder || "";
		self.control.append(placeholder);
		/**
		* TomSelect renders a custom control with a focusable <input class="items-placeholder">.
		* The source <select>'s aria-label is not automatically propagated to that input,
		* which triggers "Missing form label" accessibility warnings.
		* This helper copies the label from the <select> onto the generated input.
		*/
		const label = (_self$input = self.input) == null ? void 0 : _self$input.getAttribute("aria-label");
		if (!label) return;
		placeholder.setAttribute("aria-label", label);
	});
	self.on("initialize", () => {
		self.control_input.addEventListener("keydown", (evt) => {
			switch (evt.keyCode) {
				case KEY_ESC:
					if (self.isOpen) {
						preventDefault(evt, true);
						self.close();
					}
					self.clearActiveItems();
					return;
				case KEY_TAB: self.focus_node.tabIndex = -1;
			}
			return self.onKeyDown.call(self, evt);
		});
		self.on("blur", () => {
			self.focus_node.tabIndex = self.isDisabled ? -1 : self.tabIndex;
		});
		self.on("dropdown_open", () => {
			self.control_input.focus();
		});
		const orig_onBlur = self.onBlur;
		self.hook("instead", "onBlur", (evt) => {
			if (evt && evt.relatedTarget == self.control_input) return;
			return orig_onBlur.call(self);
		});
		addEvent(self.control_input, "blur", () => self.onBlur());
		self.hook("before", "close", () => {
			if (!self.isOpen) return;
			self.focus_node.focus({ preventScroll: true });
		});
	});
}
//#endregion
export { plugin as default };
