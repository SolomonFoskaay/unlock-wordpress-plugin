!(function () {
  "use strict";
  var e = {
      n: function (o) {
        var t =
          o && o.__esModule
            ? function () {
                return o.default;
              }
            : function () {
                return o;
              };
        return e.d(t, { a: t }), t;
      },
      d: function (o, t) {
        for (var n in t)
          e.o(t, n) &&
            !e.o(o, n) &&
            Object.defineProperty(o, n, { enumerable: !0, get: t[n] });
      },
      o: function (e, o) {
        return Object.prototype.hasOwnProperty.call(e, o);
      },
    },
    o = window.wp.element,
    t = window.wp.i18n,
    n = window.wp.blocks,
    l = window.wp.blockEditor,
    c = window.wp.components,
    r = window.React,
    a = window.wp.apiFetch,
    s = e.n(a);
  const k = (e) =>
    -1 === e.network ||
    (!!e.address && !!new RegExp("^0x[a-fA-F0-9]{40}$", "g").test(e.address));
  (0, n.registerBlockType)("unlock-protocol/unlock-box", {
    title: (0, t.__)("Unlock Protocol", "unlock-protocol"),
    category: "common",
    icon: "lock",
    description: (0, t.__)(
      "A block to add lock(s) to the content inside of WordPress.",
      "unlock-protocol"
    ),
    attributes: {
      locks: { type: "array", default: [] },
      ethereumNetworks: { type: "array", default: [] },
    },
    supports: { align: !0 },
    edit: function (e) {
      let { attributes: a, setAttributes: u } = e;
      const { locks: d, ethereumNetworks: i } = a,
        p = (0, n.getBlockTypes)()
          .map((e) => e.name)
          .filter((e) => "unlock-protocol/unlock-box" !== e);
      (0, r.useEffect)(() => {
        s()({ path: "/unlock-protocol/v1/settings" })
          .then((e) => {
            let o = e.networks,
              n = [{ label: (0, t.__)("None", "unlock-protocol"), value: -1 }];
            Object.entries(o).forEach((e) => {
              let [o, t] = e;
              n.push({ label: t.network_name, value: t.network_id });
            }),
              u({ ethereumNetworks: n });
          })
          .catch((e) => {});
      }, []);
      const m = (e, o, t) => {
        (d[e][o] = t), u({ locks: [...d] });
      };
      return (0, o.createElement)(
        o.Fragment,
        null,
        (0, o.createElement)(
          "div",
          (0, l.useBlockProps)(),
          (0, o.createElement)(
            l.InspectorControls,
            null,
            (0, o.createElement)(
              c.PanelBody,
              { title: (0, t.__)("Locks", "unlock-protocol") },
              d.map((e, n) =>
                (0, o.createElement)(
                  "div",
                  { class: "setting-lock" },
                  (0, o.createElement)(c.SelectControl, {
                    label: (0, t.__)("Network", "unlock-protocol"),
                    value: e.network,
                    options: i,
                    onChange: (e) => m(n, "network", parseInt(e)),
                  }),
                  -1 !== e.network
                    ? (0, o.createElement)(
                        o.Fragment,
                        null,
                        (0, o.createElement)(
                          "p",
                          { className: "block-label" },
                          (0, t.__)("Lock Address", "unlock-protocol")
                        ),
                        (0, o.createElement)(c.TextControl, {
                          value: e.address,
                          onChange: (e) => m(n, "address", e),
                        })
                      )
                    : "",
                  !k(e) &&
                    (0, o.createElement)(
                      "p",
                      { className: "lock-warning" },
                      (0, t.__)("Lock address is not valid", "unlock-protocol")
                    ),
                  (0, o.createElement)(
                    c.Button,
                    {
                      isSmall: !0,
                      isDestructive: !0,
                      onClick: () => {
                        ((e) => {
                          const o = [...d];
                          o.splice(e, 1), u({ locks: o });
                        })(n);
                      },
                    },
                    "Remove"
                  )
                )
              ),
              (0, o.createElement)(
                c.PanelRow,
                null,
                (0, o.createElement)(
                  c.Button,
                  {
                    className: "add-lock",
                    variant: "primary",
                    onClick: () => {
                      u(
                        d
                          ? { locks: [...d, { address: "", network: -1 }] }
                          : { locks: [{ address: "", network: -1 }] }
                      );
                    },
                  },
                  "Add Lock"
                )
              ),
              (0, o.createElement)(
                "div",
                { className: "docs" },
                (0, o.createElement)(
                  "a",
                  {
                    rel: "noopener noreferrer",
                    target: "_blank",
                    href: unlockProtocol.unlock_docs.docs,
                  },
                  (0, t.__)("Unlock's documentation", "unlock-protocol")
                ),
                (0, o.createElement)("br", null),
                (0, o.createElement)(
                  "a",
                  {
                    rel: "noopener noreferrer",
                    target: "_blank",
                    href: unlockProtocol.unlock_docs.deploy_lock,
                  },
                  (0, t.__)("Deploy a lock", "unlock-protocol")
                )
              )
            )
          ),
          (0, o.createElement)("div", { className: "unlock-header-icon" }),
          ((e) => {
            let o = !0;
            return (
              0 === e.length && (o = !1),
              e.forEach((e) => {
                (-1 !== e.network && k(e)) || (o = !1);
              }),
              o
            );
          })(d)
            ? (wp.data.dispatch("core/editor").unlockPostSaving("my-lock"),
              (0, o.createElement)(l.InnerBlocks, { allowedBlocks: p }))
            : (wp.data.dispatch("core/editor").lockPostSaving("my-lock"),
              (0, o.createElement)(
                "div",
                { className: "no-lock-address" },
                (0, o.createElement)(
                  "p",
                  null,
                  (0, t.__)(
                    "Please configure the lock(s) on this block.",
                    "unlock-protocol"
                  )
                )
              ))
        )
      );
    },
    save: () => (0, o.createElement)(l.InnerBlocks.Content, null),
  });
})();
