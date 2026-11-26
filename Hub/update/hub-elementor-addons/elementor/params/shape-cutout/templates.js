function render_lqd_cutout() {
  var out = "";
  var el_id = view.model.get("id");
  var mask_id = "lqd-cutout-mask-" + el_id;
  var circle_mask_id_bs = "lqd-cutout-mask-circle-bs-" + el_id;
  var circle_mask_id_te = "lqd-cutout-mask-circle-te-" + el_id;
  var style = settings.lqd_shape_cutout_style;
  var placement =
    style == "style-2"
      ? settings.lqd_shape_cutout_placement_2 || "b"
      : settings.lqd_shape_cutout_placement || "br";

  var classnames = [
    "lqd-cutout",
    "lqd-cutout-" + (style == "style-2" ? "2" : "1"),
    "lqd-cutout-" + placement,
    "lqd-cutout-" + el_id,
    "pointer-events-none",
  ];

  view.addRenderAttribute(`shape-cutout-${el_id}`, {
    class: classnames,
  });

  const renderAttributes = view.getRenderAttributeString(
    `shape-cutout-${el_id}`,
  );

  if (style == "style-1") {
    out = `
<style>
	.elementor-element-${el_id} {
		mask: linear-gradient(0deg, black, black), url(#${mask_id});
		mask-composite: exclude;
	}
</style>
<div ${renderAttributes}>
	<svg
		 class="lqd-cutout-svg pos-abs pos-tl w-100 h-100 w-full h-full pointer-events-none z-index--1"
		 role="none"
		 width="100"
		 height="100"
		 fill="none"
		 xmlns="http://www.w3.org/2000/svg">
		<mask id="${circle_mask_id_bs}">
			<circle class="lqd-cutout-mask-circle lqd-cutout-mask-circle-bs lqd-cutout-mask-circle-bs-mask-fill" cx="50" cy="50" r="50" fill="white" />
			<circle class="lqd-cutout-mask-circle lqd-cutout-mask-circle-bs lqd-cutout-mask-circle-bs-mask-clip" cx="50" cy="50" r="50" fill="black" />
		</mask>
		<mask id="${circle_mask_id_te}">
			<circle class="lqd-cutout-mask-circle lqd-cutout-mask-circle-te lqd-cutout-mask-circle-te-mask-fill" cx="50" cy="50" r="50" fill="white" />
			<circle class="lqd-cutout-mask-circle lqd-cutout-mask-circle-te lqd-cutout-mask-circle-te-mask-clip" cx="50" cy="50" r="50" fill="black" />
		</mask>

		<defs>
			<mask id="${mask_id}">
				<g class="lqd-cutout-mask-g-wrap">
					<g class="lqd-cutout-mask-g">
						<rect class="lqd-cutout-mask-rect" width="760" height="160" rx="38" fill="white"/>
						<circle class="lqd-cutout-mask-circle lqd-cutout-mask-circle-bs" cx="50" cy="50" r="50" fill="white" mask="url(#${circle_mask_id_bs})" />
						<circle class="lqd-cutout-mask-circle lqd-cutout-mask-circle-te" cx="50" cy="50" r="50" fill="white" mask="url(#${circle_mask_id_te})" />
					</g>
				</g>
			</mask>
		</defs>
	</svg>
</div>`;
  } else if (style == "style-2") {
    var placements = placement == "tb" ? ["t", "b"] : [placement];
    // Mask is composited with `exclude`: white = hole, black = keep.
    var half = `
<rect class="lqd-cutout-mask-rect" width="100" height="100" rx="20" fill="white"/>
<rect class="lqd-cutout-mask-shoulder" width="100" height="100" fill="white"/>
<circle class="lqd-cutout-mask-shoulder-clip" cx="50" cy="50" r="50" fill="black"/>`;
    var groups = placements
      .map(function (p) {
        return `
				<g class="lqd-cutout-mask-g-wrap lqd-cutout-mask-g-wrap-${p}">
					<g class="lqd-cutout-mask-g">
						<g class="lqd-cutout-mask-half lqd-cutout-mask-half-r">${half}</g>
						<g class="lqd-cutout-mask-half-l">
							<g class="lqd-cutout-mask-half">${half}</g>
						</g>
					</g>
				</g>`;
      })
      .join("");

    out = `
<style>
	.elementor-element-${el_id} {
		mask: linear-gradient(0deg, black, black), url(#${mask_id});
		mask-composite: exclude;
	}
</style>
<div ${renderAttributes}>
	<svg
		 class="lqd-cutout-svg pos-abs pos-tl w-100 h-100 w-full h-full pointer-events-none z-index--1"
		 role="none"
		 width="100"
		 height="100"
		 fill="none"
		 xmlns="http://www.w3.org/2000/svg">
		<defs>
			<mask id="${mask_id}">${groups}
			</mask>
		</defs>
	</svg>
</div>`;
  }

  return out;
}
