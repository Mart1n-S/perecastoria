import React from 'react';
import { Box, Typography } from '@mui/material';

const Result = ({ storyData, audioUrl, imageBase64 }) => {
    return (
        <Box
            mt={4}
            p={3}
            bgcolor="rgba(255,255,255,0.05)"
            borderRadius={2}
            width="80%"
            maxWidth="600px"
            boxShadow={3}
            display="flex"
            flexDirection="column"
            alignItems="center"
        >
            {/* Affichage de l'image */}
            {imageBase64 && (
                <Box
                    component="img"
                    src={`data:image/jpeg;base64,${imageBase64}`}
                    alt="Illustration générée"
                    sx={{
                        width: '100%',
                        borderRadius: 2,
                        mb: 2,
                    }}
                />
            )}

            {/* Affichage de l'histoire */}
            <Typography variant="body1" color="white" textAlign="justify" sx={{ mb: 2,     height: '200px', overflowY: 'auto', paddingRight: '10px' }}>
                {storyData}
            </Typography>

            {/* Affichage de l'audio */}
            {audioUrl && (
                <audio controls style={{ width: '100%' }}>
                    <source src={`data:audio/wav;base64,${audioUrl}`} type="audio/wav" />
                    Ton navigateur ne supporte pas la lecture audio.
                </audio>
            )}
        </Box>
    );
};

export default Result;
