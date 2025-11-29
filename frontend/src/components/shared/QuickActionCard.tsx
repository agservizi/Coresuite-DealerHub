import Link from "next/link";
import type { ReactNode } from "react";

interface Props {
  title: string;
  description: string;
  href: string;
  icon: ReactNode;
}

export function QuickActionCard({ title, description, href, icon }: Props) {
  return (
    <Link
      href={href}
      className="glass-card flex items-center gap-4 rounded-xl border border-gray-200 p-4 text-gray-900 transition hover:border-emerald-400/50"
    >
      <div className="rounded-full bg-emerald-500/10 p-3 text-emerald-600">
        {icon}
      </div>
      <div>
        <p className="text-base font-semibold">{title}</p>
        <p className="text-sm text-gray-500">{description}</p>
      </div>
    </Link>
  );
}
