"use client";

import { Navbar } from "@/components/layout/Navbar";
import { Sidebar } from "@/components/layout/Sidebar";
import { useAuthGuard } from "@/hooks/useAuthGuard";
import type { ReactNode } from "react";

export function AppShell({ children }: { children: ReactNode }) {
  const { loading } = useAuthGuard();

  if (loading) {
    return (
      <div className="flex min-h-screen items-center justify-center text-white">
        Caricamento sessione...
      </div>
    );
  }

  return (
    <div className="flex min-h-screen bg-slate-950 text-slate-100">
      <Sidebar />
      <div className="flex flex-1 flex-col">
        <Navbar />
        <main className="flex-1 bg-linear-to-b from-slate-950 via-slate-900 to-slate-950 px-4 py-8">
          {children}
        </main>
      </div>
    </div>
  );
}
