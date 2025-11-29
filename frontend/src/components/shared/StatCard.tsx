import type { ReactNode } from "react";

interface Props {
  label: string;
  value: string | number;
  icon?: ReactNode;
  highlight?: boolean;
}

export function StatCard({ label, value, icon, highlight }: Props) {
  return (
    <div
      className={`glass-card flex flex-col gap-2 p-4 ${
        highlight ? "border-emerald-400/50" : ""
      }`}
    >
      <div className="flex items-center justify-between text-xs uppercase tracking-wide text-slate-400">
        {label}
        {icon}
      </div>
      <p className="text-3xl font-semibold text-white">{value}</p>
    </div>
  );
}
