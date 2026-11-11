<?php

namespace RadmanCT\Integrations\Elementor\ContainerMotion;

defined( 'ABSPATH' ) || exit;

class Motion_Control extends \Elementor\Modules\AtomicWidgets\Controls\Base\Element_Control_Base {
	private string $group = 'motion';
	private bool $layout = false;
	public function get_type(): string { return 'rdct-motion'; }
	public function configure( string $group, bool $layout ): self { $this->group = $group; $this->layout = $layout; return $this; }
	public function get_props(): array { return array( 'group' => $this->group, 'layout' => $this->layout ); }
}
