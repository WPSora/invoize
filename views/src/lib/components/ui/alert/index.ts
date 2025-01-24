import { tv, type VariantProps } from "tailwind-variants";

import Root from "./alert.svelte";
import Description from "./alert-description.svelte";
import Title from "./alert-title.svelte";

export const alertVariants = tv({
	base: "relative w-full rounded-lg border px-4 py-3 text-sm [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground",
	variants: {
		variant: {
			default: "bg-background text-foreground",
			destructive:
				"border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive",
			warning: "bg-yellow-200 border-yellow-300 text-yellow-600 dark:border-yellow-400 [&>svg]:text-yellow-600"
		}
	},
	defaultVariants: {
		variant: "default"
	}
});

export type Variant = VariantProps<typeof alertVariants>["variant"];
export type HeadingLevel = "h1" | "h2" | "h3" | "h4" | "h6" | "h6";

export {
	Root,
	Description,
	Title,
	//
	Root as Alert,
	Description as AlertDescription,
	Title as AlertTitle
};
