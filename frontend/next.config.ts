import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  output: "export",
  images: {
    unoptimized: true,
  },
  trailingSlash: true,
  eslint: {
    // Hostinger build pipeline should not fail if lint warnings appear
    ignoreDuringBuilds: true,
  },
  experimental: {
    reactCompiler: false,
  },
};

export default nextConfig;
