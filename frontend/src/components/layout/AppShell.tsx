"use client";

import { Navbar } from "@/components/layout/Navbar";
import { Sidebar } from "@/components/layout/Sidebar";
import { useAuthGuard } from "@/hooks/useAuthGuard";
import { useState } from "react";
import type { ReactNode } from "react";

export function AppShell({ children }: { children: ReactNode }) {
  const { loading } = useAuthGuard();
  const [mobileSidebarOpen, setMobileSidebarOpen] = useState(false);

  if (loading) {
    return (
      <div className="flex min-h-screen items-center justify-center text-white">
        Caricamento sessione...
      </div>
    );
  }

  return (
    <div className="flex min-h-screen bg-blue-950 text-white">
      <Sidebar isOpen={mobileSidebarOpen} onClose={() => setMobileSidebarOpen(false)} />
      <div className="flex flex-1 flex-col">
        <Navbar onMenuClick={() => setMobileSidebarOpen(true)} />
        <main className="flex-1 bg-linear-to-b from-blue-950 via-blue-900 to-blue-950 px-4 py-8">
          {children}
        </main>
      </div>
    </div>
  );
}
