"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@/context/AuthContext";
import type { UserRole } from "@/types";

interface Options {
  requireAuth?: boolean;
  allowedRoles?: UserRole[];
}

export function useAuthGuard({ requireAuth = true, allowedRoles }: Options = {}) {
  const router = useRouter();
  const { user, loading } = useAuth();

  useEffect(() => {
    if (loading) return;

    if (requireAuth && !user) {
      router.replace("/login");
      return;
    }

    if (allowedRoles && user && !allowedRoles.includes(user.role)) {
      router.replace("/dashboard");
    }
  }, [user, loading, allowedRoles, requireAuth, router]);

  return { user, loading, isAuthorized: Boolean(user) };
}
