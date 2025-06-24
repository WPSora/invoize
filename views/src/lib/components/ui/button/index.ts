import type { Button as ButtonPrimitive } from "bits-ui";
import { tv, type VariantProps } from "tailwind-variants";
import Root from "./button.svelte";

const buttonVariants = tv({
	base: "inline-flex items-center justify-center rounded-md text-sm font-medium whitespace-nowrap transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50",
	variants: {
		variant: {
			default:
				"text-white shadow hover:bg-primary/90 hover:text-white-foreground hover:shadow-lg hover:shadow-primary/80 transition-all duration-300 bg-gradient-to-tr from-primary to-primary-600 hover:bg-gradient-to-t",
			destructive:
				"text-destructive-foreground shadow-sm hover:bg-red-500/90 hover:shadow-md hover:shadow-destructive/80 transition-all duration-300 bg-gradient-to-tr from-destructive to-red-400",
			outline:
				"border border-input bg-transparent shadow-sm hover:bg-accent hover:text-accent-foreground hover:shadow-lg transition-all duration-300",
			secondary:
				"bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80 hover:shadow-md transition-all duration-300",
			ghost: "hover:bg-accent hover:text-accent-foreground",
			link: "text-primary underline-offset-4 hover:underline"
		},
		size: {
			default: "h-9 px-4 py-2",
			sm: "h-8 rounded-md px-3 text-xs",
			lg: "h-10 rounded-md px-8",
			icon: "h-9 w-9"
		}
	},
	defaultVariants: {
		variant: "default",
		size: "default"
	}
});

type Variant = VariantProps<typeof buttonVariants>["variant"];
type Size = VariantProps<typeof buttonVariants>["size"];

type Props = ButtonPrimitive.Props & {
	variant?: Variant;
	size?: Size;
};

type Events = ButtonPrimitive.Events;

export {
	Root,
	type Props,
	type Events,
	//
	Root as Button,
	type Props as ButtonProps,
	type Events as ButtonEvents,
	buttonVariants
};
