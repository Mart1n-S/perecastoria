import React from 'react';
import { Box, Typography, Button, IconButton } from '@mui/material';
import { PlayArrow } from '@mui/icons-material';

const Result = ({ storyData, audioUrl, imageBase64 }) => {
  const handlePlayAudio = () => {
    const audio = new Audio(audioUrl);
    audio.play();
  };

  return (
    <Box
      sx={{
        width: '100%',
        maxWidth: '600px',
        bgcolor: 'rgba(255, 255, 255, 0.1)',
        backdropFilter: 'blur(10px)',
        p: 4,
        borderRadius: 3,
        boxShadow: 3,
        textAlign: 'center',
      }}
    >
      <Typography variant="h5" color="white" paragraph>
        {storyData}
      </Typography>

      <Box sx={{ mb: 3 }}>
        <img src={`data:image/jpeg;base64,${imageBase64}`} alt="Generated" style={{ width: '100%', borderRadius: 8 }} />
      </Box>

      {audioUrl && (
        <Box>
          <IconButton color="primary" onClick={handlePlayAudio}>
            <PlayArrow />
          </IconButton>
          <Typography variant="body1" color="white" mt={1}>
            Click to Play Audio
          </Typography>
        </Box>
      )}
    </Box>
  );
};

export default Result;
