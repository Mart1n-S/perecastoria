import { Box, Button, TextField, Typography, Select, FormControl, OutlinedInput, MenuItem } from "@mui/material";
import { useState } from "react";

const Home = () => {
  const [description, setDescription] = useState("");
  const [language, setLanguage] = useState("FR");

  const handleLanguageChange = (e) => {
    setLanguage(e.target.value);
  };

  const handleGenerate = async () => {
    try {
      const payload = {
        prompt: description,
        langue: language,
      };
  
      const response = await fetch('https://perecastoria.fr/perecastoria-back/api-v1/llm.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
      });
  
      if (response.ok) {
        const data = await response.json();
        console.log("Réponse du backend:", data);
      } else {
        console.error("Erreur lors de la requête:", response.statusText);
      }
    } catch (error) {
      console.error("Une erreur est survenue:", error);
    }
  };
  

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
          name="promptField"
          value={description}
          onChange={(e) => setDescription(e.target.value)}
          sx={{
            bgcolor: "white",
            borderRadius: 1,
            mb: 3,
          }}
        />

        <Box display="flex" fullWidth justifyContent="space-between">
          <Button variant="contained" sx={{ width: '75%' }} color="primary" onClick={handleGenerate}>
            Generate
          </Button>
          <FormControl sx={{ width: 'auto' }}>
            <Select
              labelId="langue-label"
              id="langue"
              value={language}
              onChange={handleLanguageChange}
              displayEmpty
              sx={{
                color: 'white',
                border: '1px solid white',
                '& .MuiSelect-icon': {
                  color: 'white',
                },
                '&:hover': {
                  borderColor: 'white',
                },
                '& .MuiOutlinedInput-notchedOutline': {
                  borderColor: 'white',
                },
              }}
              input={<OutlinedInput label="Langue" />}
            >
              <MenuItem value="FR">
                <Box display="flex" alignItems="center" gap={1}>
                  <img src="./assets/france.svg" alt="France" width="20" />
                  FR
                </Box>
              </MenuItem>

              <MenuItem value="EN">
                <Box display="flex" alignItems="center" gap={1}>
                  <img src="./assets/english.svg" alt="English" width="20" />
                  EN
                </Box>
              </MenuItem>

              <MenuItem value="ESP">
                <Box display="flex" alignItems="center" gap={1}>
                  <img src="./assets/espagnol.svg" alt="Espagnol" width="20" />
                  ESP
                </Box>
              </MenuItem>
            </Select>
          </FormControl>
        </Box>
      </Box>
    </Box>
  );
};

export default Home;
