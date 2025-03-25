import React, { useState } from 'react';
import { Box, Button, TextField, Typography, Select, FormControl, OutlinedInput, MenuItem, CircularProgress } from "@mui/material";
import Result from './Result';

const Home = () => {
  const [description, setDescription] = useState("");
  const [language, setLanguage] = useState("FR");
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState(null);

  const handleLanguageChange = (e) => {
    setLanguage(e.target.value);
  };

  const handleGenerate = async () => {
    setLoading(true);
    try {
      const payload = {
        prompt: description,
        language: language,
      };

      const response = await fetch('https://perecastoria.fr/perecastoria-back/orchestrator.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
      });

      if (response.ok) {
        const data = await response.json();
        setResult(data);
      } else {
        console.error("Erreur lors de la requête:", response.statusText);
      }
    } catch (error) {
      console.error("Une erreur est survenue:", error);
    } finally {
      setLoading(false);
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
            {loading ? <CircularProgress size={24} sx={{color: "red"}} thickness={10} /> : "Generate"}
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
              <MenuItem value="FR">FR</MenuItem>
              <MenuItem value="EN">EN</MenuItem>
              <MenuItem value="ESP">ESP</MenuItem>
            </Select>
          </FormControl>
        </Box>

        {result && <Result storyData={result.story_data} audioUrl={result.audio_url} imageBase64={result.image_base_64} />}
      </Box>
    </Box>
  );
};

export default Home;
