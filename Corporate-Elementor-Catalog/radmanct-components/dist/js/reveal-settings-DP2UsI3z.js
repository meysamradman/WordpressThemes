//#region radmanct-components/src/js/components/text/reveal-settings.js
var numeric = (value, fallback, min, max) => value !== void 0 && value !== "" && Number.isFinite(Number(value)) ? Math.min(max, Math.max(min, Number(value))) : fallback;
var position = (value, fallback) => /^(?:top|center|bottom|(?:100|[0-9]{1,2}(?:\.[0-9]{1,2})?)%) (?:top|center|bottom|(?:100|[0-9]{1,2}(?:\.[0-9]{1,2})?)%)$/.test(value) ? value : fallback;
function revealTween(root, mask = false) {
	const data = root.dataset;
	const fill = data.rdctTextReveal === "fill";
	return {
		from: fill ? { backgroundSize: "0% 100%, 100% 100%" } : {
			opacity: numeric(data.initialOpacity, .25, .1, 1),
			...mask ? { yPercent: 100 } : {}
		},
		to: {
			...fill ? { backgroundSize: "100% 100%, 100% 100%" } : {
				opacity: numeric(data.finalOpacity, 1, .1, 1),
				...mask ? { yPercent: 0 } : {}
			},
			ease: "none",
			stagger: {
				each: numeric(data.revealStagger, .12, 0, 1),
				from: [
					"start",
					"end",
					"center",
					"edges",
					"random"
				].includes(data.revealFrom) ? data.revealFrom : "start"
			},
			scrollTrigger: {
				trigger: root,
				start: position(data.revealStart, "top 80%"),
				end: position(data.revealEnd, "bottom 35%"),
				scrub: numeric(data.revealScrub, .5, 0, 5),
				invalidateOnRefresh: true
			}
		}
	};
}
//#endregion
export { revealTween as t };
