// resources/js/tailwind-config.js
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
    // ============================================
    // LIGHT THEME — Sunlit Forest Academic
    // ============================================
    "surface":                     "#f7f8f5",
    "surface-dim":                 "#eef1e8",
    "surface-bright":              "#ffffff",
    "surface-container-lowest":    "#ffffff",
    "surface-container-low":       "#ffffff",
    "surface-container":           "#f2f4ee",
    "surface-container-high":      "#eef1e8",
    "surface-container-highest":   "#e8ede2",
    "surface-variant":             "#eef1e8",

    "on-surface":                  "#1a2416",
    "on-surface-variant":          "#5d6b56",
    "inverse-surface":             "#1a2416",
    "inverse-on-surface":          "#f7f8f5",

    "outline":                     "#8a9a83",
    "outline-variant":             "#c4d0bc",

    "primary":                     "#3d6b1f",
    "on-primary":                  "#ffffff",
    "primary-container":           "#8bae66",
    "on-primary-container":        "#ffffff",
    "primary-fixed":               "#c9ee9f",
    "primary-fixed-dim":           "#8bae66",

    "secondary":                   "#628141",
    "on-secondary":                "#ffffff",
    "secondary-container":         "#d4e6bc",
    "on-secondary-container":      "#2a4d15",

    "tertiary":                    "#c9973f",
    "on-tertiary":                 "#ffffff",
    "tertiary-container":          "#f6e0b5",
    "on-tertiary-container":       "#453819",

    "error":                       "#a3405d",
    "on-error":                    "#ffffff",

    "background":                  "#f7f8f5",
    "on-background":               "#1a2416",

    "success":                     "#628141",
    "warning":                     "#c9973f",
},
            borderRadius: {
                DEFAULT: "0.25rem",
                lg: "0.5rem",
                xl: "0.75rem",
                full: "9999px"
            },
            spacing: {
                "space-md": "1rem",
                "page-margin-desktop": "2rem",
                "space-xs": "0.5rem",
                "space-2xs": "0.25rem",
                "page-margin-mobile": "1rem",
                "bento-gap-desktop": "1.25rem",
                "space-xl": "2rem",
                "bento-gap-mobile": "0.75rem",
                "space-2xl": "3rem",
                "space-lg": "1.5rem",
                "space-sm": "0.75rem"
            },
            fontFamily: {
                "headline-md": ["Plus Jakarta Sans"],
                "label-md": ["Inter"],
                "headline-lg": ["Plus Jakarta Sans"],
                "headline-sm": ["Plus Jakarta Sans"],
                "display-lg": ["Plus Jakarta Sans"],
                "body-md": ["Plus Jakarta Sans"],
                "label-lg": ["Inter"],
                "label-sm": ["Inter"],
                "body-sm": ["Plus Jakarta Sans"],
                "body-lg": ["Plus Jakarta Sans"]
            },
            fontSize: {
                "headline-md": ["22px", { lineHeight: "30px", letterSpacing: "-0.01em", fontWeight: "600" }],
                "label-md": ["11px", { lineHeight: "14px", letterSpacing: "0.02em", fontWeight: "600" }],
                "headline-lg": ["28px", { lineHeight: "36px", letterSpacing: "-0.01em", fontWeight: "600" }],
                "headline-sm": ["18px", { lineHeight: "26px", fontWeight: "600" }],
                "display-lg": ["44px", { lineHeight: "52px", letterSpacing: "-0.02em", fontWeight: "700" }],
                "body-md": ["14px", { lineHeight: "20px", fontWeight: "400" }],
                "label-lg": ["13px", { lineHeight: "16px", letterSpacing: "0.01em", fontWeight: "600" }],
                "label-sm": ["10px", { lineHeight: "12px", letterSpacing: "0.03em", fontWeight: "600" }],
                "body-sm": ["12px", { lineHeight: "18px", fontWeight: "400" }],
                "body-lg": ["16px", { lineHeight: "24px", fontWeight: "400" }]
            }
        }
    }
};