//#region radmanct/inc/components/motion-easings.json
var motion_easings_default = [
	"none",
	"linear",
	"power1.in",
	"power2.in",
	"power3.in",
	"power4.in",
	"sine.in",
	"expo.in",
	"circ.in",
	"back.in",
	"bounce.in",
	"elastic.in(1,0.2)",
	"power1.out",
	"power2.out",
	"power3.out",
	"power4.out",
	"sine.out",
	"expo.out",
	"circ.out",
	"back.out",
	"bounce.out",
	"elastic.out(1,0.2)",
	"power1.inOut",
	"power2.inOut",
	"power3.inOut",
	"power4.inOut",
	"sine.inOut",
	"expo.inOut",
	"circ.inOut",
	"back.inOut",
	"bounce.inOut",
	"elastic.inOut(1,0.2)"
];
//#endregion
//#region radmanct/src/js/components/container-motion-options.js
var number = (value, fallback, min, max) => {
	if (typeof value !== "number" && typeof value !== "string") return fallback;
	if (typeof value === "string" && !/^[+-]?(?:\d+\.?\d*|\.\d+)(?:e[+-]?\d+)?$/i.test(value.trim())) return fallback;
	return Number.isFinite(Number(value)) ? Math.min(max, Math.max(min, Number(value))) : fallback;
};
var choice = (value, values, fallback) => values.includes(value) ? value : fallback;
var axes = [
	"x",
	"y",
	"z",
	"scaleX",
	"scaleY",
	"rotationX",
	"rotationY",
	"rotation",
	"opacity"
];
var layouts = "[data-element_type=\"container\"], [data-element_type=\"section\"], [data-element_type=\"column\"], [data-e-type=\"e-flexbox\"], [data-e-type=\"e-grid\"], [data-e-type=\"e-div-block\"]";
var motionElements = `${layouts}, .elementor-widget[data-id], [data-e-type][data-id]`;
function scrollPosition(value, fallback) {
	if (typeof value !== "string" || value.length > 120) return fallback;
	const normalized = value.trim().toLowerCase().replace(/\s+/g, " ");
	const number = "[+-]?(?:\\d+(?:\\.\\d+)?|\\.\\d+)(?:%|px)?";
	const anchor = `(?:top|center|bottom|left|right|${number})(?:[+-]=${number})?`;
	return new RegExp(`^(?:${anchor} ${anchor}|[+-]=${number}|max|${number})$`).test(normalized) ? normalized : fallback;
}
function motionOptions(input = {}) {
	const result = {
		horizontal: input.horizontal === true || input.horizontal === "yes",
		horizontal_direction: choice(input.horizontal_direction, [
			"auto",
			"left",
			"right"
		], "auto"),
		horizontal_width: number(input.horizontal_width, 90, 10, 100),
		horizontal_gap: number(input.horizontal_gap, 24, 0, 200),
		horizontal_align: choice(input.horizontal_align, [
			"start",
			"center",
			"end",
			"stretch"
		], "start"),
		horizontal_offset: number(input.horizontal_offset, 0, 0, 300),
		horizontal_min_width: number(input.horizontal_min_width, 1024, 320, 1920),
		horizontal_scrub: number(input.horizontal_scrub, 1, .1, 5),
		reveal: input.reveal === true || input.reveal === "yes",
		reveal_direction: choice(input.reveal_direction, [
			"start",
			"end",
			"left",
			"right"
		], "start"),
		reveal_start: number(input.reveal_start, 90, 50, 100),
		reveal_end: number(input.reveal_end, 50, 0, 80),
		reveal_scrub: number(input.reveal_scrub, 1, .1, 5),
		animate: input.animate === true || input.animate === "yes",
		parallax: input.parallax === true || input.parallax === "yes",
		inner: input.inner === void 0 || input.inner === null || input.inner === true || input.inner === "yes",
		targets: choice(input.targets, [
			"contents",
			"containers",
			"self"
		], "contents"),
		preset: choice(input.preset, [
			"custom",
			"fade",
			"fade-up",
			"fade-down",
			"fade-left",
			"fade-right",
			"flip-x",
			"flip-y",
			"scale-up",
			"scale-down"
		], "custom"),
		ease: choice(input.ease, motion_easings_default, "power4.out"),
		order: choice(input.order, [
			"start",
			"end",
			"center",
			"edges",
			"random"
		], "start"),
		duration: number(input.duration, 1.6, 0, 10),
		stagger: number(input.stagger, .16, 0, 10),
		delay: number(input.delay, 0, 0, 10),
		scrub: number(input.scrub, 1, .1, 5),
		parallax_ease: choice(input.parallax_ease, motion_easings_default, "linear"),
		parallax_perspective: number(input.parallax_perspective, 0, 0, 2e3),
		parallax_distance: number(input.parallax_distance, 0, -500, 500),
		parallax_distance_unit: choice(input.parallax_distance_unit, ["px", "%"], "px"),
		parallax_start: choice(input.parallax_start, [
			"top bottom",
			"top top",
			"center center"
		], "top bottom"),
		parallax_end: choice(input.parallax_end, ["bottom top", "bottom bottom"], "bottom top")
	};
	if (input.parallax_start === "custom") result.parallax_start = scrollPosition(input.parallax_start_custom, "top bottom");
	if (input.parallax_end === "custom") result.parallax_end = scrollPosition(input.parallax_end_custom, "bottom top");
	else if (result.parallax_distance) result.parallax_end = result.parallax_end.replace(/^(\S+)/, `$1${result.parallax_distance > 0 ? "+" : "-"}=${Math.abs(result.parallax_distance)}${result.parallax_distance_unit}`);
	for (const group of [
		"from",
		"to",
		"parallax_from",
		"parallax_to"
	]) {
		result[group] = {};
		for (const axis of axes) {
			const scale = axis.startsWith("scale");
			const opacity = axis === "opacity";
			let fallback = scale || opacity ? 1 : 0;
			result[group][axis] = number(input[`${group}_${axis}`], fallback, scale || opacity ? 0 : -1e3, opacity ? 1 : scale ? 5 : 1e3);
			if (axis === "x" || axis === "y") {
				const unit = choice(input[`${group}_${axis}_unit`], [
					"px",
					"%",
					"vw",
					"vh"
				], "px");
				if (unit !== "px") result[group][axis] = `${number(input[`${group}_${axis}`], fallback, -100, 100)}${unit}`;
			}
		}
		if (input[`${group}_origin`] === true || input[`${group}_origin`] === "yes") {
			const coordinate = (axis) => {
				const unit = choice(input[`${group}_origin${axis}_unit`], ["%", "px"], "%");
				const limit = unit === "%" ? 100 : 500;
				return `${number(input[`${group}_origin${axis}`], 50, -limit, limit)}${unit}`;
			};
			result[group].transformOrigin = `${coordinate("X")} ${coordinate("Y")} ${number(input[`${group}_originZ`], 0, -500, 500)}px`;
		}
	}
	if (result.parallax_perspective > 0) result.parallax_from.transformPerspective = result.parallax_perspective;
	if (result.preset !== "custom") {
		result.from = Object.fromEntries(axes.map((axis) => [axis, axis.startsWith("scale") ? 1 : 0]));
		result.to = {
			...result.from,
			opacity: 1
		};
		Object.assign(result.from, {
			"fade-up": { y: 150 },
			"fade-down": { y: -150 },
			"fade-left": { x: -150 },
			"fade-right": { x: 150 },
			"flip-x": {
				y: 150,
				rotationX: -30
			},
			"flip-y": {
				x: 150,
				rotationY: 30
			},
			"scale-up": {
				scaleX: .75,
				scaleY: .75
			},
			"scale-down": {
				scaleX: 1.25,
				scaleY: 1.25
			}
		}[result.preset]);
	}
	result.reveal_end = Math.min(result.reveal_end, result.reveal_start - 5);
	return result;
}
function revealClip(direction, rtl) {
	return direction === "left" || direction === "start" && !rtl || direction === "end" && rtl ? "inset(0 100% 0 0)" : "inset(0 0 0 100%)";
}
function motionTargets(root, options) {
	if (options.targets === "self") return root.hasAttribute("data-rdct-native-motion") ? [] : [root];
	const host = [...root.children].find((node) => node.matches(".e-con-inner, .elementor-container, .elementor-widget-wrap")) || root;
	let candidates;
	if (options.targets === "containers") candidates = [...host.children].filter((node) => node.matches(layouts));
	else if (!options.inner) candidates = [...host.children];
	else {
		candidates = [...root.querySelectorAll(".elementor-widget, [data-e-type], [data-rdct-motion-item]")].filter((node) => !node.matches(layouts));
		if (!candidates.length) candidates = [...host.children];
	}
	const targets = [...new Set(candidates)].filter((node) => !node.matches("script, style, template") && node.closest("[data-rdct-motion], [data-rdct-native-motion]") === root);
	const selected = new Set(targets);
	return targets.filter((node) => {
		for (let parent = node.parentElement; parent && parent !== root; parent = parent.parentElement) if (selected.has(parent)) return false;
		return !node.querySelector("[data-rdct-motion], [data-rdct-native-motion]");
	}).slice(0, 200);
}
//#endregion
export { revealClip as a, motionTargets as i, motionElements as n, motionOptions as r, layouts as t };
