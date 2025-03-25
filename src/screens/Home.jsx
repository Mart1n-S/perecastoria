import { Box, Button, TextField, Typography, Select, MenuItem, InputLabel } from "@mui/material";
import { useState } from "react";
import LanguageSelect from "../components/LanguageSelect";

const Home = () => {
  const [description, setDescription] = useState("");

  return (
    <Box
      display="flex"
      justifyContent="center"
      alignItems="center"
      height="100vh"
      width="100vw"
      sx={{
        background: "linear-gradient(to top, black, #222, black)",
      }}
    >
      <Box
        sx={{
          width: "400px",
          bgcolor: "rgba(255, 255, 255, 0.1)",
          backdropFilter: "blur(10px)",
          p: 4,
          borderRadius: 3,
          boxShadow: 3,
        }}
      >
        <Typography variant="h4" align="center" color="white" fontWeight="bold" gutterBottom>
          Père CastorIA
        </Typography>

        <TextField
          fullWidth
          multiline
          rows={3}
          placeholder="Describe a movie..."
          variant="filled"
          value={description}
          onChange={(e) => setDescription(e.target.value)}
          sx={{
            bgcolor: "white",
            borderRadius: 1,
            mb: 3,
          }}
        />

        <Box display="flex" fullWidth justifyContent="space-between">
          <Button variant="contained" sx={{width: '75%'}}  color="primary">
            Generate
          </Button>
          <LanguageSelect />
        </Box>
      </Box>
    </Box>
  );
};

export default Home;
