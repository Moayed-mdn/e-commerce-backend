declare namespace App.DTOs {
export type HeroBannerData = {
id: number;
title: string | null;
subtitle: string | null;
cat_text: string | null;
cat_url: string;
position: number;
visual: { type: "image"; img_url: string } | { type: "gradient"; gradient_from: string; gradient_to: string };
};
}
