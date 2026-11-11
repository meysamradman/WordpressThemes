import { d as elementTransitionEnd, m as getSlideTransformEl } from "./utils-rsb8sxsd.js";
//#region node_modules/swiper/shared/effect-init.mjs
function effectInit(params) {
	const { effect, swiper, on, setTranslate, setTransition, overwriteParams, perspective, recreateShadows, getEffectParams } = params;
	on("beforeInit", () => {
		if (swiper.params.effect !== effect) return;
		swiper.classNames.push(`${swiper.params.containerModifierClass}${effect}`);
		if (perspective && perspective()) swiper.classNames.push(`${swiper.params.containerModifierClass}3d`);
		const overwriteParamsResult = overwriteParams ? overwriteParams() : {};
		Object.assign(swiper.params, overwriteParamsResult);
		Object.assign(swiper.originalParams, overwriteParamsResult);
	});
	on("setTranslate _virtualUpdated", () => {
		if (swiper.params.effect !== effect) return;
		setTranslate();
	});
	on("setTransition", (_s, duration) => {
		if (swiper.params.effect !== effect) return;
		setTransition(duration);
	});
	on("transitionEnd", () => {
		if (swiper.params.effect !== effect) return;
		if (recreateShadows) {
			const effectParams = getEffectParams ? getEffectParams() : void 0;
			if (!effectParams || !effectParams.slideShadows) return;
			swiper.slides.forEach((slideEl) => {
				slideEl.querySelectorAll(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").forEach((shadowEl) => shadowEl.remove());
			});
			recreateShadows();
		}
	});
	let requireUpdateOnVirtual = false;
	on("virtualUpdate", () => {
		if (swiper.params.effect !== effect) return;
		if (!swiper.slides.length) requireUpdateOnVirtual = true;
		requestAnimationFrame(() => {
			if (requireUpdateOnVirtual && swiper.slides && swiper.slides.length) {
				setTranslate();
				requireUpdateOnVirtual = false;
			}
		});
	});
}
//#endregion
//#region node_modules/swiper/shared/effect-target.mjs
function effectTarget(_effectParams, slideEl) {
	const transformEl = getSlideTransformEl(slideEl);
	if (transformEl !== slideEl) {
		transformEl.style.backfaceVisibility = "hidden";
		transformEl.style.setProperty("-webkit-backface-visibility", "hidden");
	}
	return transformEl;
}
//#endregion
//#region node_modules/swiper/shared/effect-virtual-transition-end.mjs
function effectVirtualTransitionEnd({ swiper, duration, transformElements, allSlides }) {
	const { activeIndex } = swiper;
	const getSlide = (el) => {
		if (!el.parentElement) return swiper.slides.find((slideEl) => slideEl.shadowRoot && slideEl.shadowRoot === el.parentNode);
		if (el.parentElement instanceof HTMLElement) return el.parentElement;
	};
	if (swiper.params.virtualTranslate && duration !== 0) {
		let eventTriggered = false;
		let transitionEndTarget;
		if (allSlides) transitionEndTarget = transformElements;
		else transitionEndTarget = transformElements.filter((transformEl) => {
			const el = transformEl.classList.contains("swiper-slide-transform") ? getSlide(transformEl) : transformEl;
			return !!el && swiper.getSlideIndex(el) === activeIndex;
		});
		transitionEndTarget.forEach((el) => {
			elementTransitionEnd(el, () => {
				if (eventTriggered) return;
				if (!swiper || swiper.destroyed) return;
				eventTriggered = true;
				swiper.animating = false;
				const evt = new CustomEvent("transitionend", {
					bubbles: true,
					cancelable: true
				});
				swiper.wrapperEl.dispatchEvent(evt);
			});
		});
	}
}
//#endregion
export { effectTarget as n, effectInit as r, effectVirtualTransitionEnd as t };
