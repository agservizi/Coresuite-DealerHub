"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { Building2, FileText, Gauge, Shield, Upload } from "lucide-react";
import { useAuth } from "@/context/AuthContext";

const baseLinks = [{ label: "Dashboard", href: "/dashboard", icon: Gauge }];

const affiliateLinks = [
  { label: "I miei contratti", href: "/contracts", icon: FileText },
  { label: "Nuovo contratto", href: "/contracts/new", icon: Upload },
  { label: "Controllo copertura", href: "/coverage", icon: Shield },
];

const adminLinks = [
  { label: "Gestione contratti", href: "/contracts", icon: FileText },
  { label: "Affiliati", href: "/affiliates", icon: Building2 },
];

export function Sidebar() {
  const pathname = usePathname();
  const { user } = useAuth();

  const links = [
    ...baseLinks,
    ...(user?.role === "AFFILIATO" ? affiliateLinks : []),
    ...(user?.role === "SUPERADMIN" ? adminLinks : []),
  ];

  return (
    <aside className="hidden min-h-screen w-64 flex-col bg-slate-900/70 px-4 py-8 text-slate-200 md:flex sticky top-0">
      <div className="mb-8 text-xl font-semibold">DealerHub</div>
      <nav className="flex flex-1 flex-col gap-2">
        {links.map(({ href, icon: Icon, label }) => {
          const active = pathname.startsWith(href);
          return (
            <Link
              key={href}
              href={href}
              className={`flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition hover:bg-slate-800/60 ${
                active ? "bg-slate-800 text-white" : "text-slate-300"
              }`}
            >
              <Icon size={18} /> {label}
            </Link>
          );
        })}
      </nav>
    </aside>
  );
}
