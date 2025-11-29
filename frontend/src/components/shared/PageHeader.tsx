import type { ReactNode } from "react";

interface Props {
  title: string;
  description?: string;
  cta?: ReactNode;
}

export function PageHeader({ title, description, cta }: Props) {
  return (
    <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 className="text-2xl font-semibold text-white">{title}</h1>
        {description && <p className="text-sm text-slate-400">{description}</p>}
      </div>
      {cta}
    </div>
  );
}
