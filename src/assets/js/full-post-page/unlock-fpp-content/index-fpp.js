import { __ } from "@wordpress/i18n";
import { registerPlugin } from "@wordpress/plugins";
import { addMetaBox } from "@wordpress/edit-post";
import Edit from "./edit-fpp";

/**
 * Register the meta box.
 */
addMetaBox("unlock-protocol", {
  title: __("Unlock Protocol", "unlock-protocol"),

  context: "side",

  icon: "lock",

  description: __(
    "Lock or unlock the full post/page with Unlock Protocol.",
    "unlock-protocol"
  ),

  attributes: {
    locks: {
      type: "array",
      default: [],
    },
    ethereumNetworks: {
      type: "array",
      default: [],
    },
  },

  /**
   * @see ./edit.js
   */
  render: Edit,
});

/**
 * Register the plugin.
 */
registerPlugin("unlock-protocol", {
  render: () => null,
});


