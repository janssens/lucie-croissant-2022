
  import { defineConfig } from "tinacms";
  
  // Your hosting provider likely exposes this as an environment variable
  const branch = process.env.HEAD || process.env.VERCEL_GIT_COMMIT_REF || "master";
  
  export default defineConfig({
    branch,
    clientId: process.env.TINA_CLIENT_ID,
    token: process.env.TINA_TOKEN,
    build: {
      outputFolder: "admin",
      publicFolder: "static",
    },
    media: {
      tina: {
        mediaRoot: "uploads",
        publicFolder: "public",
      },
    },
    schema: {
      collections: [
        {
          name: "pages",
          label: "Pages",
          path: "content/pages",
          fields: [
            {
              type: "string",
              name: "title",
              label: "Title",
              isTitle: true,
              required: true,
            },
            {
              type: "rich-text",
              name: "body",
              label: "Body",
              isBody: true,
            },
          ],
        },
      ],
    },
  });
  